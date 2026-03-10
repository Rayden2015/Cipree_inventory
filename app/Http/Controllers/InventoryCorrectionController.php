<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Inventory;
use App\Models\InventoryCorrectionRequest;
use App\Models\InventoryCorrectionReasonCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;

class InventoryCorrectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List pending correction requests (Supervisor view).
     */
    public function index(Request $request)
    {
        $this->authorize('approve-inventory-correction');
        $site_id = Auth::user()->site->id ?? null;
        $tenant_id = session('current_tenant_id') ?? Auth::user()->getCurrentTenant()?->id;

        $query = InventoryCorrectionRequest::with(['inventoryItem.item', 'inventoryItem.inventory', 'requestedByUser', 'reasonCode'])
            ->where('status', InventoryCorrectionRequest::STATUS_PENDING);

        if ($tenant_id) {
            $query->where('tenant_id', $tenant_id);
        }
        if ($site_id) {
            $query->where('site_id', $site_id);
        }

        $requests = $query->latest()->paginate(20);
        return view('inventory-corrections.index', compact('requests'));
    }

    /**
     * Show form to flag an inventory line for correction (Stores Officer).
     */
    public function create(Request $request, $inventory_item_id)
    {
        $this->authorize('initiate-inventory-correction');
        $item = InventoryItem::with(['item', 'inventory', 'location'])
            ->where('id', $inventory_item_id)
            ->whereIn('status', [InventoryItem::STATUS_ACTIVE])
            ->firstOrFail();

        $site_id = Auth::user()->site->id ?? null;
        if ($site_id && $item->site_id != $site_id) {
            abort(403, 'You can only flag corrections for your site.');
        }

        return view('inventory-corrections.create', compact('item'));
    }

    /**
     * Store a new correction request (Stores Officer submits).
     */
    public function store(Request $request)
    {
        $this->authorize('initiate-inventory-correction');
        $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'intended_quantity' => 'required|numeric|min:0',
            'intended_unit_cost_exc_vat_gh' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $inventoryItem = InventoryItem::findOrFail($request->inventory_item_id);
        if ($inventoryItem->status !== InventoryItem::STATUS_ACTIVE) {
            Toastr::error('Only Active lines can be flagged for correction.');
            return redirect()->back();
        }

        $user = Auth::user();
        if ($user->site_id && $inventoryItem->site_id != $user->site->id) {
            abort(403, 'You can only flag corrections for your site.');
        }

        InventoryCorrectionRequest::create([
            'inventory_item_id' => $inventoryItem->id,
            'requested_by' => $user->id,
            'intended_quantity' => $request->intended_quantity,
            'intended_unit_cost_exc_vat_gh' => $request->intended_unit_cost_exc_vat_gh ?? $inventoryItem->unit_cost_exc_vat_gh,
            'status' => InventoryCorrectionRequest::STATUS_PENDING,
            'notes' => $request->notes,
            'tenant_id' => $inventoryItem->tenant_id,
            'site_id' => $inventoryItem->site_id,
        ]);

        Toastr::success('Correction request submitted. A Supervisor must approve it.');
        return redirect()->route('inventories.edit', $inventoryItem->inventory_id)->with('success', 'Correction request submitted.');
    }

    /**
     * Show approve/reject form (Supervisor) with shortfall warning if applicable.
     */
    public function show($id)
    {
        $this->authorize('approve-inventory-correction');
        $correctionRequest = InventoryCorrectionRequest::with(['inventoryItem.item', 'inventoryItem.inventory', 'requestedByUser'])
            ->findOrFail($id);

        if ($correctionRequest->status !== InventoryCorrectionRequest::STATUS_PENDING) {
            Toastr::warning('This request has already been processed.');
            return redirect()->route('inventory-corrections.index');
        }

        $issuedQty = (int) DB::table('sorder_parts')->where('inventory_id', $correctionRequest->inventory_item_id)->sum('qty_supplied');
        $shortfall = $correctionRequest->intended_quantity - $issuedQty;
        $reasonCodes = InventoryCorrectionReasonCode::orderBy('code')->get();

        return view('inventory-corrections.show', compact('correctionRequest', 'issuedQty', 'shortfall', 'reasonCodes'));
    }

    /**
     * Approve and execute correction (void + mirror + new, or negative adjustment if shortfall).
     */
    public function approve(Request $request, $id)
    {
        $this->authorize('execute-inventory-adjustment');
        $request->validate([
            'reason_code_id' => 'required|exists:inventory_correction_reason_codes,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $correctionRequest = InventoryCorrectionRequest::with('inventoryItem.inventory')->findOrFail($id);
        if (!$correctionRequest->isPending()) {
            Toastr::error('Request already processed.');
            return redirect()->route('inventory-corrections.index');
        }

        $original = $correctionRequest->inventoryItem;
        $issuedQty = (int) DB::table('sorder_parts')->where('inventory_id', $original->id)->sum('qty_supplied');
        $shortfall = $correctionRequest->intended_quantity - $issuedQty;

        DB::beginTransaction();
        try {
            $correctionRequest->update([
                'status' => InventoryCorrectionRequest::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'reason_code_id' => $request->reason_code_id,
                'notes' => $request->notes,
            ]);

            if ($shortfall < 0) {
                // Scenario B: Items already issued – create negative adjustment only; original stays Active (parent of issued).
                $adjustmentQty = $original->quantity - $correctionRequest->intended_quantity;
                InventoryItem::create([
                    'inventory_id' => $original->inventory_id,
                    'location_id' => $original->location_id,
                    'item_id' => $original->item_id,
                    'quantity' => -$adjustmentQty,
                    'unit_cost_exc_vat_gh' => $original->unit_cost_exc_vat_gh,
                    'unit_cost_exc_vat_usd' => $original->unit_cost_exc_vat_usd,
                    'amount' => -$adjustmentQty * ($original->unit_cost_exc_vat_gh ?? 0),
                    'total_value_gh' => $original->total_value_gh ? -$adjustmentQty * $original->unit_cost_exc_vat_gh : null,
                    'total_value_usd' => $original->total_value_usd ? -$adjustmentQty * ($original->unit_cost_exc_vat_usd ?? 0) : null,
                    'site_id' => $original->site_id,
                    'tenant_id' => $original->tenant_id,
                    'status' => InventoryItem::STATUS_ADJUSTMENT,
                    'source_inventory_item_id' => $original->id,
                    'description' => $original->description,
                    'part_number' => $original->part_number,
                    'stock_code' => $original->stock_code,
                    'discount' => $original->discount,
                    'before_discount' => $original->before_discount,
                ]);
                Log::info('InventoryCorrectionController: Scenario B negative adjustment', ['request_id' => $id, 'inventory_item_id' => $original->id]);
            } else {
                // Scenario A: Void + Mirror + New
                $original->update(['status' => InventoryItem::STATUS_VOIDED]);

                $mirrorQty = -$original->quantity;
                $mirrorAmount = $mirrorQty * ($original->unit_cost_exc_vat_gh ?? 0);
                InventoryItem::create([
                    'inventory_id' => $original->inventory_id,
                    'location_id' => $original->location_id,
                    'item_id' => $original->item_id,
                    'quantity' => $mirrorQty,
                    'unit_cost_exc_vat_gh' => $original->unit_cost_exc_vat_gh,
                    'unit_cost_exc_vat_usd' => $original->unit_cost_exc_vat_usd,
                    'amount' => $mirrorAmount,
                    'total_value_gh' => $original->total_value_gh ? $mirrorAmount : null,
                    'total_value_usd' => $original->total_value_usd ? $mirrorAmount : null,
                    'site_id' => $original->site_id,
                    'tenant_id' => $original->tenant_id,
                    'status' => InventoryItem::STATUS_ADJUSTMENT,
                    'source_inventory_item_id' => $original->id,
                    'description' => $original->description,
                    'part_number' => $original->part_number,
                    'stock_code' => $original->stock_code,
                    'discount' => $original->discount,
                    'before_discount' => $original->before_discount,
                ]);

                $intendedCost = $correctionRequest->intended_unit_cost_exc_vat_gh ?? $original->unit_cost_exc_vat_gh;
                $newAmount = $correctionRequest->intended_quantity * $intendedCost;
                $inv = $original->inventory;
                $totalValueUsd = ($inv && $inv->billing_currency === 'Dollar') ? round($newAmount, 2) : null;
                $totalValueGh = $inv && $inv->exchange_rate ? round($newAmount * $inv->exchange_rate, 2) : $newAmount;
                InventoryItem::create([
                    'inventory_id' => $original->inventory_id,
                    'location_id' => $original->location_id,
                    'item_id' => $original->item_id,
                    'quantity' => $correctionRequest->intended_quantity,
                    'unit_cost_exc_vat_gh' => $intendedCost,
                    'unit_cost_exc_vat_usd' => $totalValueUsd,
                    'amount' => $newAmount,
                    'total_value_gh' => $totalValueGh,
                    'total_value_usd' => $totalValueUsd,
                    'site_id' => $original->site_id,
                    'tenant_id' => $original->tenant_id,
                    'status' => InventoryItem::STATUS_ACTIVE,
                    'description' => $original->description,
                    'part_number' => $original->part_number,
                    'stock_code' => $original->stock_code,
                    'discount' => $original->discount,
                    'before_discount' => $original->before_discount,
                ]);

                Log::info('InventoryCorrectionController: Scenario A void+mirror+new', ['request_id' => $id, 'inventory_item_id' => $original->id]);
            }

            $this->recomputeStockQuantity();
            DB::commit();
            Toastr::success('Correction approved and applied.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('InventoryCorrectionController@approve', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            Toastr::error('Failed to apply correction: ' . $e->getMessage());
        }

        return redirect()->route('inventory-corrections.index');
    }

    /**
     * Reject correction request.
     */
    public function reject(Request $request, $id)
    {
        $this->authorize('approve-inventory-correction');
        $request->validate(['rejection_reason' => 'nullable|string|max:500']);

        $correctionRequest = InventoryCorrectionRequest::findOrFail($id);
        if (!$correctionRequest->isPending()) {
            Toastr::warning('Request already processed.');
            return redirect()->route('inventory-corrections.index');
        }

        $correctionRequest->update([
            'status' => InventoryCorrectionRequest::STATUS_REJECTED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        Toastr::info('Correction request rejected.');
        return redirect()->route('inventory-corrections.index');
    }

    /**
     * Recompute items.stock_quantity from Active + Adjustment lines only.
     */
    protected function recomputeStockQuantity(): void
    {
        if (app()->environment('testing')) {
            return;
        }
        DB::statement("
            UPDATE items i
            LEFT JOIN (
                SELECT t.item_id, SUM(t.quantity) AS calculated_quantity
                FROM inventory_items t
                WHERE t.status IN ('Active', 'Adjustment')
                GROUP BY t.item_id
            ) AS subquery ON i.id = subquery.item_id
            SET i.stock_quantity = COALESCE(subquery.calculated_quantity, 0)
        ");
    }

    /**
     * Auditor view: full ledger (all statuses) for an inventory or item.
     */
    public function auditLog(Request $request)
    {
        $this->authorize('view-inventory-audit-log');
        $inventory_id = $request->get('inventory_id');
        $item_id = $request->get('item_id');
        $site_id = Auth::user()->site->id ?? null;

        $query = InventoryItem::with(['item', 'inventory', 'sourceInventoryItem'])
            ->auditorAll();

        if ($inventory_id) {
            $query->where('inventory_id', $inventory_id);
        }
        if ($item_id) {
            $query->where('item_id', $item_id);
        }
        if ($site_id) {
            $query->where('site_id', $site_id);
        }

        $lines = $query->latest('id')->paginate(50);
        return view('inventory-corrections.audit-log', compact('lines'));
    }
}
