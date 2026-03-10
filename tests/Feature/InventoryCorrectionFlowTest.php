<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Department;
use App\Models\Inventory;
use App\Models\InventoryCorrectionReasonCode;
use App\Models\InventoryCorrectionRequest;
use App\Models\InventoryItem;
use App\Models\Item;
use App\Models\Location;
use App\Models\Site;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class InventoryCorrectionFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Site $site;
    protected User $storeOfficer;
    protected User $supervisor;
    protected Inventory $inventory;
    protected InventoryItem $inventoryItem;
    protected InventoryCorrectionReasonCode $reasonCode;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->tenant = Tenant::factory()->create(['name' => 'Test Tenant', 'status' => 'Active']);
        $this->site = Site::factory()->forTenant($this->tenant)->create(['name' => 'Primary', 'site_code' => 'PRIM']);

        $dept = Department::create([
            'name' => 'Stores',
            'description' => 'Stores',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->storeOfficer = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $dept->id,
            'status' => 'Active',
        ]);

        $this->supervisor = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $dept->id,
            'status' => 'Active',
        ]);

        $permissions = [
            'initiate-inventory-correction',
            'approve-inventory-correction',
            'execute-inventory-adjustment',
            'view-inventory-audit-log',
            'view-grn',
        ];
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
        $this->storeOfficer->givePermissionTo('initiate-inventory-correction');
        $this->storeOfficer->givePermissionTo('view-grn');
        $this->supervisor->givePermissionTo(['approve-inventory-correction', 'execute-inventory-adjustment', 'view-inventory-audit-log']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $supplier = Supplier::create([
            'name' => 'Acme',
            'phone' => '123',
            'email' => 'a@b.com',
            'address' => 'Addr',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $location = Location::create([
            'name' => 'WH1',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $category = Category::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $item = Item::create([
            'item_description' => 'Widget',
            'item_stock_code' => 'W-01',
            'item_part_number' => 'P-01',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'item_category_id' => $category->id,
            'amount' => 0,
            'stock_quantity' => 0,
        ]);

        $this->inventory = Inventory::create([
            'supplier_id' => $supplier->id,
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'billing_currency' => 'Cedi',
            'exchange_rate' => 1,
            'trans_type' => 'Purchase',
            'po_number' => 'PO-1',
            'date' => now(),
        ]);

        $this->inventoryItem = InventoryItem::create([
            'inventory_id' => $this->inventory->id,
            'location_id' => $location->id,
            'item_id' => $item->id,
            'quantity' => 10,
            'unit_cost_exc_vat_gh' => 20,
            'before_discount' => 200,
            'discount' => 0,
            'amount' => 200,
            'total_value_gh' => 200,
            'total_value_usd' => 200,
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'status' => InventoryItem::STATUS_ACTIVE,
        ]);

        $this->reasonCode = InventoryCorrectionReasonCode::firstOrCreate(
            ['code' => 'TYP-01'],
            ['type' => 'Data Entry', 'use_case' => 'Clerical typo.']
        );
    }

    public function test_index_requires_approve_permission(): void
    {
        $this->actingAs($this->supervisor)
            ->get(route('inventory-corrections.index'))
            ->assertStatus(200);

        $this->actingAs($this->storeOfficer)
            ->get(route('inventory-corrections.index'))
            ->assertStatus(403);
    }

    public function test_create_form_requires_initiate_permission(): void
    {
        $this->actingAs($this->storeOfficer)
            ->get(route('inventory-corrections.create', $this->inventoryItem->id))
            ->assertStatus(200);

        $userNoPerm = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'Active',
        ]);
        $this->actingAs($userNoPerm)
            ->get(route('inventory-corrections.create', $this->inventoryItem->id))
            ->assertStatus(403);
    }

    public function test_create_form_404_for_voided_line(): void
    {
        $this->inventoryItem->update(['status' => InventoryItem::STATUS_VOIDED]);

        $this->actingAs($this->storeOfficer)
            ->get(route('inventory-corrections.create', $this->inventoryItem->id))
            ->assertStatus(404);
    }

    public function test_store_creates_pending_request(): void
    {
        $this->actingAs($this->storeOfficer)
            ->post(route('inventory-corrections.store'), [
                'inventory_item_id' => $this->inventoryItem->id,
                'intended_quantity' => 7,
                'intended_unit_cost_exc_vat_gh' => 22,
                'notes' => 'Typo correction',
            ])
            ->assertRedirect(route('inventories.edit', $this->inventory->id));

        $req = InventoryCorrectionRequest::first();
        $this->assertNotNull($req);
        $this->assertEquals(InventoryCorrectionRequest::STATUS_PENDING, $req->status);
        $this->assertEquals($this->inventoryItem->id, $req->inventory_item_id);
        $this->assertEquals($this->storeOfficer->id, $req->requested_by);
        $this->assertEquals(7, $req->intended_quantity);
        $this->assertEquals(22, $req->intended_unit_cost_exc_vat_gh);
        $this->assertEquals($this->tenant->id, $req->tenant_id);
        $this->assertEquals($this->site->id, $req->site_id);
    }

    public function test_store_rejects_non_active_line(): void
    {
        $this->inventoryItem->update(['status' => InventoryItem::STATUS_VOIDED]);

        $this->actingAs($this->storeOfficer)
            ->post(route('inventory-corrections.store'), [
                'inventory_item_id' => $this->inventoryItem->id,
                'intended_quantity' => 5,
            ])
            ->assertRedirect();

        $this->assertEquals(0, InventoryCorrectionRequest::count());
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->storeOfficer)
            ->post(route('inventory-corrections.store'), [
                'inventory_item_id' => $this->inventoryItem->id,
            ])
            ->assertSessionHasErrors('intended_quantity');

        $this->actingAs($this->storeOfficer)
            ->post(route('inventory-corrections.store'), [])
            ->assertSessionHasErrors(['inventory_item_id', 'intended_quantity']);
    }

    public function test_show_approve_screen_with_shortfall(): void
    {
        $req = InventoryCorrectionRequest::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'requested_by' => $this->storeOfficer->id,
            'intended_quantity' => 5,
            'intended_unit_cost_exc_vat_gh' => 20,
            'status' => InventoryCorrectionRequest::STATUS_PENDING,
            'tenant_id' => $this->tenant->id,
            'site_id' => $this->site->id,
        ]);

        $response = $this->actingAs($this->supervisor)
            ->get(route('inventory-corrections.show', $req->id));

        $response->assertStatus(200);
        $response->assertSee('5');
        $response->assertSee((string) $this->reasonCode->code);
    }

    public function test_approve_scenario_a_void_mirror_new(): void
    {
        $req = InventoryCorrectionRequest::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'requested_by' => $this->storeOfficer->id,
            'intended_quantity' => 8,
            'intended_unit_cost_exc_vat_gh' => 25,
            'status' => InventoryCorrectionRequest::STATUS_PENDING,
            'tenant_id' => $this->tenant->id,
            'site_id' => $this->site->id,
        ]);

        $this->actingAs($this->supervisor)
            ->post(route('inventory-corrections.approve', $req->id), [
                'reason_code_id' => $this->reasonCode->id,
                'notes' => 'Approved',
            ])
            ->assertRedirect(route('inventory-corrections.index'));

        $req->refresh();
        $this->assertEquals(InventoryCorrectionRequest::STATUS_APPROVED, $req->status);
        $this->assertEquals($this->supervisor->id, $req->approved_by);
        $this->assertNotNull($req->approved_at);

        $this->inventoryItem->refresh();
        $this->assertEquals(InventoryItem::STATUS_VOIDED, $this->inventoryItem->status);

        $items = InventoryItem::where('inventory_id', $this->inventory->id)->orderBy('id')->get();
        $mirror = $items->where('status', InventoryItem::STATUS_ADJUSTMENT)->where('quantity', '<', 0)->first();
        $newActive = $items->where('status', InventoryItem::STATUS_ACTIVE)->first();

        $this->assertNotNull($mirror);
        $this->assertEquals(-10, $mirror->quantity);
        $this->assertEquals($this->inventoryItem->id, $mirror->source_inventory_item_id);

        $this->assertNotNull($newActive);
        $this->assertEquals(8, $newActive->quantity);
        $this->assertEquals(25, $newActive->unit_cost_exc_vat_gh);
    }

    public function test_approve_scenario_b_negative_adjustment(): void
    {
        $req = InventoryCorrectionRequest::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'requested_by' => $this->storeOfficer->id,
            'intended_quantity' => 3,
            'intended_unit_cost_exc_vat_gh' => 20,
            'status' => InventoryCorrectionRequest::STATUS_PENDING,
            'tenant_id' => $this->tenant->id,
            'site_id' => $this->site->id,
        ]);

        $sorder = \App\Models\Sorder::create([
            'inventory_id' => $this->inventory->id,
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'Supplied',
        ]);
        \DB::table('sorder_parts')->insert([
            'sorder_id' => $sorder->id,
            'inventory_id' => $this->inventoryItem->id,
            'item_id' => $this->inventoryItem->item_id,
            'qty_supplied' => 5,
            'quantity' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->supervisor)
            ->post(route('inventory-corrections.approve', $req->id), [
                'reason_code_id' => $this->reasonCode->id,
            ])
            ->assertRedirect(route('inventory-corrections.index'));

        $req->refresh();
        $this->assertEquals(InventoryCorrectionRequest::STATUS_APPROVED, $req->status);

        $this->inventoryItem->refresh();
        $this->assertEquals(InventoryItem::STATUS_ACTIVE, $this->inventoryItem->status);

        $adjustment = InventoryItem::where('inventory_id', $this->inventory->id)
            ->where('status', InventoryItem::STATUS_ADJUSTMENT)
            ->where('quantity', '<', 0)
            ->first();
        $this->assertNotNull($adjustment);
        $this->assertEquals(-7, $adjustment->quantity);
        $this->assertEquals($this->inventoryItem->id, $adjustment->source_inventory_item_id);
    }

    public function test_approve_requires_reason_code(): void
    {
        $req = InventoryCorrectionRequest::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'requested_by' => $this->storeOfficer->id,
            'intended_quantity' => 5,
            'status' => InventoryCorrectionRequest::STATUS_PENDING,
            'tenant_id' => $this->tenant->id,
            'site_id' => $this->site->id,
        ]);

        $this->actingAs($this->supervisor)
            ->post(route('inventory-corrections.approve', $req->id), [])
            ->assertSessionHasErrors('reason_code_id');

        $req->refresh();
        $this->assertEquals(InventoryCorrectionRequest::STATUS_PENDING, $req->status);
    }

    public function test_approve_requires_execute_permission(): void
    {
        $userApproveOnly = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'Active',
        ]);
        $userApproveOnly->givePermissionTo('approve-inventory-correction');

        $req = InventoryCorrectionRequest::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'requested_by' => $this->storeOfficer->id,
            'intended_quantity' => 5,
            'status' => InventoryCorrectionRequest::STATUS_PENDING,
            'tenant_id' => $this->tenant->id,
            'site_id' => $this->site->id,
        ]);

        $this->actingAs($userApproveOnly)
            ->post(route('inventory-corrections.approve', $req->id), [
                'reason_code_id' => $this->reasonCode->id,
            ])
            ->assertStatus(403);
    }

    public function test_reject_sets_status_and_redirects(): void
    {
        $req = InventoryCorrectionRequest::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'requested_by' => $this->storeOfficer->id,
            'intended_quantity' => 5,
            'status' => InventoryCorrectionRequest::STATUS_PENDING,
            'tenant_id' => $this->tenant->id,
            'site_id' => $this->site->id,
        ]);

        $this->actingAs($this->supervisor)
            ->post(route('inventory-corrections.reject', $req->id), [
                'rejection_reason' => 'Not justified',
            ])
            ->assertRedirect(route('inventory-corrections.index'));

        $req->refresh();
        $this->assertEquals(InventoryCorrectionRequest::STATUS_REJECTED, $req->status);
        $this->assertEquals('Not justified', $req->rejection_reason);
        $this->assertEquals($this->supervisor->id, $req->approved_by);
    }

    public function test_reject_requires_approve_permission(): void
    {
        $req = InventoryCorrectionRequest::create([
            'inventory_item_id' => $this->inventoryItem->id,
            'requested_by' => $this->storeOfficer->id,
            'intended_quantity' => 5,
            'status' => InventoryCorrectionRequest::STATUS_PENDING,
            'tenant_id' => $this->tenant->id,
            'site_id' => $this->site->id,
        ]);

        $this->actingAs($this->storeOfficer)
            ->post(route('inventory-corrections.reject', $req->id), ['rejection_reason' => 'No'])
            ->assertStatus(403);
    }

    public function test_audit_log_requires_permission(): void
    {
        $this->actingAs($this->supervisor)
            ->get(route('inventory-corrections.audit-log'))
            ->assertStatus(200);

        $userNoPerm = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'Active',
        ]);
        $this->actingAs($userNoPerm)
            ->get(route('inventory-corrections.audit-log'))
            ->assertStatus(403);
    }

    public function test_audit_log_lists_ledger_lines(): void
    {
        $this->actingAs($this->supervisor)
            ->get(route('inventory-corrections.audit-log'))
            ->assertStatus(200)
            ->assertSee('Widget');
    }
}
