<?php

namespace App\Http\Controllers;

use App\Models\Enduser;
use App\Models\Site;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;

class WorkOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-work-order'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:add-work-order'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:edit-work-order'])->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $site_id = Auth::user()->site->id ?? null;
        $tenant_id = Auth::user()->getCurrentTenant()?->id;

        $query = WorkOrder::query();

        if ($tenant_id) {
            $query->where('tenant_id', $tenant_id);
        }

        if ($site_id) {
            $query->where('site_id', $site_id);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('work_order_number', 'like', $search)
                    ->orWhere('title', 'like', $search);
            });
        }

        $workOrders = $query->with(['asset', 'responsiblePerson', 'storeRequests'])
            ->latest('requested_date')
            ->paginate(20)
            ->appends($request->all());

        return view('work-orders.index', compact('workOrders'));
    }

    public function create()
    {
        $site_id = Auth::user()->site->id ?? null;
        $tenant_id = Auth::user()->getCurrentTenant()?->id;

        // Assets: equipment / machines
        $assets = Enduser::query()
            ->when($tenant_id, fn($q) => $q->where('tenant_id', $tenant_id))
            ->when($site_id, fn($q) => $q->where('site_id', $site_id))
            ->whereIn('type', ['Equipment', 'Machine'])
            ->orderBy('name_description')
            ->get();

        // Responsible people: individual/personnel (non-machine)
        $people = Enduser::query()
            ->when($tenant_id, fn($q) => $q->where('tenant_id', $tenant_id))
            ->when($site_id, fn($q) => $q->where('site_id', $site_id))
            ->whereNotIn('type', ['Equipment', 'Machine'])
            ->orderBy('name_description')
            ->get();

        return view('work-orders.create', compact('assets', 'people'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:Low,Medium,High,Critical',
            'requested_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:requested_date',
            'asset_enduser_id' => 'nullable|exists:endusers,id',
            'responsible_enduser_id' => 'required|exists:endusers,id',
        ]);

        $user = Auth::user();
        $site_id = $user->site->id ?? null;
        $tenant_id = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;

        // Auto-generate a unique work order number per tenant/site context
        $prefixParts = ['WO'];
        if ($user->site && $user->site->site_code) {
            $prefixParts[] = strtoupper($user->site->site_code);
        }
        $prefixParts[] = now()->format('Ymd');
        $prefix = implode('-', $prefixParts);

        $counter = 1;
        do {
            $candidate = sprintf('%s-%03d', $prefix, $counter);
            $exists = WorkOrder::where('work_order_number', $candidate)->exists();
            $counter++;
        } while ($exists && $counter < 1000);

        $workOrderNumber = $candidate;

        $workOrder = WorkOrder::create([
            'work_order_number' => $workOrderNumber,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'Open',
            'priority' => $request->priority,
            'requested_date' => $request->requested_date ?? now(),
            'due_date' => $request->due_date,
            'asset_enduser_id' => $request->asset_enduser_id,
            'responsible_enduser_id' => $request->responsible_enduser_id,
            'site_id' => $site_id,
            'tenant_id' => $tenant_id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        Log::info('WorkOrderController@store - Work order created', [
            'user_id' => $user->id,
            'work_order_id' => $workOrder->id,
        ]);

        Toastr::success('Work order created successfully.');

        return redirect()->route('work-orders.index');
    }

    public function show(WorkOrder $workOrder)
    {
        $this->authorizeWorkOrder($workOrder);

        $workOrder->load(['asset', 'responsiblePerson']);

        return view('work-orders.show', compact('workOrder'));
    }

    public function edit(WorkOrder $workOrder)
    {
        $this->authorizeWorkOrder($workOrder);

        $site_id = Auth::user()->site->id ?? null;
        $tenant_id = Auth::user()->getCurrentTenant()?->id;

        $assets = Enduser::query()
            ->when($tenant_id, fn($q) => $q->where('tenant_id', $tenant_id))
            ->when($site_id, fn($q) => $q->where('site_id', $site_id))
            ->whereIn('type', ['Equipment', 'Machine'])
            ->orderBy('name_description')
            ->get();

        $people = Enduser::query()
            ->when($tenant_id, fn($q) => $q->where('tenant_id', $tenant_id))
            ->when($site_id, fn($q) => $q->where('site_id', $site_id))
            ->whereNotIn('type', ['Equipment', 'Machine'])
            ->orderBy('name_description')
            ->get();

        return view('work-orders.edit', compact('workOrder', 'assets', 'people'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $this->authorizeWorkOrder($workOrder);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:Low,Medium,High,Critical',
            'status' => 'required|string|in:Open,In Progress,Completed,Cancelled',
            'requested_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:requested_date',
            'completed_date' => 'nullable|date',
            'asset_enduser_id' => 'nullable|exists:endusers,id',
            'responsible_enduser_id' => 'required|exists:endusers,id',
        ]);

        $user = Auth::user();

        $workOrder->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'requested_date' => $request->requested_date ?? $workOrder->requested_date,
            'due_date' => $request->due_date,
            'completed_date' => $request->completed_date,
            'asset_enduser_id' => $request->asset_enduser_id,
            'responsible_enduser_id' => $request->responsible_enduser_id,
            'updated_by' => $user->id,
        ]);

        Toastr::success('Work order updated successfully.');

        return redirect()->route('work-orders.show', $workOrder);
    }

    protected function authorizeWorkOrder(WorkOrder $workOrder): void
    {
        $user = Auth::user();
        $tenant_id = $user->getCurrentTenant()?->id;

        if ($tenant_id && $workOrder->tenant_id !== $tenant_id) {
            abort(403);
        }
    }
}

