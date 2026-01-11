<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Site;
use App\Models\User;
use App\Models\Item;
use App\Models\Enduser;
use App\Models\Section;
use App\Models\Supplier;
use App\Models\Location;
use App\Models\Department;
use App\Models\Tenant;
use App\Models\EndUsersCategory;
use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\InventoryItemDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

class InventoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Site $site;
    protected Department $department;
    protected Section $section;
    protected User $user;
    protected Supplier $supplier;
    protected Location $location;
    protected Item $item;
    protected Enduser $enduser;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create tenant first
        $this->tenant = Tenant::factory()->create([
            'name' => 'Test Tenant',
            'status' => 'Active',
        ]);

        // Create site with tenant
        $this->site = Site::factory()->forTenant($this->tenant)->create([
            'name' => 'Primary',
            'site_code' => 'PRIM',
        ]);

        $this->department = Department::create([
            'name' => 'Operations',
            'description' => 'Operations Department',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->section = Section::create([
            'name' => 'Ops Section',
            'description' => 'Main Section',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->user = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Acme Supplies',
            'phone' => '123456789',
            'email' => 'supplier@example.com',
            'address' => '123 Supply Street',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->location = Location::create([
            'name' => 'Main Warehouse',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $category = EndUsersCategory::create([
            'name' => 'Staff',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->enduser = Enduser::create([
            'asset_staff_id' => 'AS-100',
            'name' => 'Jane Receiver',
            'name_description' => 'Jane Receiver',
            'type' => 'Staff',
            'department' => 'Operations',
            'section' => 'Ops Section',
            'model' => null,
            'serial_number' => null,
            'manufacturer' => null,
            'designation' => 'Officer',
            'status' => 'Active',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'section_id' => $this->section->id,
            'enduser_category_id' => $category->id,
        ]);

        $this->item = Item::create([
            'item_description' => 'Laptop',
            'item_stock_code' => 'LP-01',
            'item_part_number' => 'PART-01',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'amount' => 100,
            'stock_quantity' => 0,
        ]);
    }

    public function test_store_creates_inventory_and_items()
    {
        $payload = [
            'supplier_id' => $this->supplier->id,
            'trans_type' => 'Purchase',
            'enduser_id' => $this->enduser->id,
            'delivered_by' => $this->user->id,
            'billing_currency' => 'Cedi',
            'exchange_rate' => 1,
            'request_number' => 'REQ-100',
            'products' => ['Laptop'],
            'quantity' => [5],
            'unit_cost_exc_vat_gh' => [10],
            'discount' => [0],
            'location_id' => [$this->location->id],
            'item_id' => [$this->item->id],
        ];

        $response = $this->actingAs($this->user)->post(route('inventories.store'), $payload);

        $response->assertStatus(302)->assertSessionHas('success');

        $inventory = Inventory::first();
        $this->assertNotNull($inventory);
        $this->assertEquals($this->supplier->id, $inventory->supplier_id);
        $this->assertEquals('Cedi', $inventory->billing_currency);

        $inventoryItem = InventoryItem::first();
        $this->assertNotNull($inventoryItem);
        $this->assertEquals(5, $inventoryItem->quantity);
        $this->assertEquals(10, $inventoryItem->unit_cost_exc_vat_gh);
        $this->assertEquals(50, $inventoryItem->amount);
        $this->assertEquals(50, $inventoryItem->total_value_gh);
        $this->assertEquals(50, $inventoryItem->total_value_usd);
        $this->assertEquals($this->user->id, $inventoryItem->last_updated_by);
        $this->assertNotNull($inventoryItem->last_updated_at);

        $this->assertDatabaseHas('inventory_item_details', [
            'inventory_id' => $inventory->id,
            'item_id' => $this->item->id,
            'amount' => 50,
        ]);
    }

    public function test_store_rejects_negative_quantity()
    {
        $payload = [
            'supplier_id' => $this->supplier->id,
            'trans_type' => 'Purchase',
            'enduser_id' => $this->enduser->id,
            'delivered_by' => $this->user->id,
            'billing_currency' => 'Cedi',
            'exchange_rate' => 1,
            'request_number' => 'REQ-200',
            'products' => ['Laptop'],
            'quantity' => [-5],
            'unit_cost_exc_vat_gh' => [10],
            'discount' => [0],
            'location_id' => [$this->location->id],
            'item_id' => [$this->item->id],
        ];

        $response = $this->actingAs($this->user)->post(route('inventories.store'), $payload);

        $response->assertStatus(302)->assertSessionHas('error');
        $this->assertEquals(0, Inventory::count());
        $this->assertEquals(0, InventoryItem::count());
        $this->assertEquals(0, InventoryItemDetail::count());
    }
}
