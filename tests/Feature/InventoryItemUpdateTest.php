<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Site;
use App\Models\User;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Location;
use App\Models\InventoryItem;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Http\Controllers\InventoryController;

class InventoryItemUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected Site $site;
    protected Department $department;
    protected User $user;
    protected Supplier $supplier;
    protected Location $location;
    protected Item $item;
    protected Inventory $inventory;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->tenant = \App\Models\Tenant::factory()->create([
            'name' => 'Test Tenant',
            'status' => 'Active',
        ]);
        
        $this->site = Site::factory()->forTenant($this->tenant)->create([
            'name' => 'Primary',
            'site_code' => 'PRIM',
        ]);

        $this->department = Department::create([
            'name' => 'Warehouse',
            'description' => 'Warehouse Department',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Acme Supplies',
            'phone' => '123456789',
            'email' => 'supplier@example.com',
            'address' => '123 Street',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->location = Location::create([
            'name' => 'Main Store',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $category = \App\Models\Category::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->item = Item::create([
            'item_description' => 'Test Item',
            'item_stock_code' => 'CODE-1',
            'item_part_number' => 'PART-1',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'item_category_id' => $category->id,
            'amount' => 100,
            'stock_quantity' => 10,
        ]);

        $this->user = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
        ]);

        $permissions = ['view-grn', 'edit-grn'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        $this->user->givePermissionTo($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->inventory = Inventory::create([
            'supplier_id' => $this->supplier->id,
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'billing_currency' => 'Cedi',
            'exchange_rate' => 1,
            'trans_type' => 'Purchase',
            'po_number' => 'PO-1',
            'date' => now(),
        ]);
    }

    public function test_update_inventory_item_tracks_last_updated_by()
    {
        $inventoryItem = InventoryItem::create([
            'inventory_id' => $this->inventory->id,
            'location_id' => $this->location->id,
            'item_id' => $this->item->id,
            'quantity' => 5,
            'unit_cost_exc_vat_gh' => 20,
            'before_discount' => 100,
            'discount' => 0,
            'amount' => 100,
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $payload = [
            'quantity' => 10,
            'unit_cost_exc_vat_gh' => 25,
            'discount' => 10,
            'description' => 'Updated Description',
            'uom' => 'EA',
            'part_number' => 'NEWPART',
            'stock_code' => 'NEWCODE',
            'location_id' => $this->location->id,
            'item_id' => $this->item->id,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('inventories.update_inventory_item', $inventoryItem->id), $payload);

        $response->assertRedirect();

        $inventoryItem->refresh();

        $this->assertEquals($this->user->id, $inventoryItem->last_updated_by);
        $this->assertNotNull($inventoryItem->last_updated_at);
        $this->assertEquals(10, $inventoryItem->quantity);
        $this->assertEquals(25, $inventoryItem->unit_cost_exc_vat_gh);

        $expectedBeforeDiscount = 10 * 25;
        $expectedAmount = $expectedBeforeDiscount - (($payload['discount'] / 100) * $expectedBeforeDiscount);

        $this->assertEquals($expectedBeforeDiscount, $inventoryItem->before_discount);
        $this->assertEquals($expectedAmount, $inventoryItem->amount);
        $this->assertEquals($expectedAmount, $inventoryItem->total_value_gh);

        $this->assertDatabaseHas('update_inventory_item', [
            'user_id' => $this->user->id,
        ]);
    }

    public function test_edit_view_displays_last_updated_by()
    {
        $updater = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
            'name' => 'Unit Tester',
        ]);

        $inventoryItem = InventoryItem::create([
            'inventory_id' => $this->inventory->id,
            'location_id' => $this->location->id,
            'item_id' => $this->item->id,
            'quantity' => 5,
            'unit_cost_exc_vat_gh' => 20,
            'before_discount' => 100,
            'discount' => 0,
            'amount' => 100,
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'last_updated_by' => $updater->id,
            'last_updated_at' => now()->subDay(),
        ]);

        $this->actingAs($this->user);
        $controller = app(InventoryController::class);
        $view = $controller->edit($this->inventory->id);
        $this->assertEquals('inventories.edit', $view->getName());

        $items = $view->getData()['inventory_items']->getCollection();
        $matched = $items->firstWhere('id', $inventoryItem->id);

        $this->assertNotNull($matched);
        $this->assertEquals('Unit Tester', optional($matched->lastUpdatedBy)->name);
    }
}
