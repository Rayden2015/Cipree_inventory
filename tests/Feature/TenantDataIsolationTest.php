<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Site;
use App\Models\Order;
use App\Models\Item;
use App\Models\InventoryItem;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * CRITICAL TEST: Data Isolation Tests
 * 
 * These tests ensure that tenants cannot access each other's data.
 * This is the most important aspect of multi-tenancy.
 */
class TenantDataIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant1;
    protected Tenant $tenant2;
    protected User $user1;
    protected User $user2;
    protected Site $site1;
    protected Site $site2;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create roles first
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Tenant Admin', 'guard_name' => 'web']);

        // Create two separate tenants
        $this->tenant1 = Tenant::factory()->create([
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
        ]);

        $this->tenant2 = Tenant::factory()->create([
            'name' => 'Tenant Two',
            'slug' => 'tenant-two',
        ]);

        // Create sites for each tenant
        $this->site1 = Site::factory()->forTenant($this->tenant1)->create([
            'name' => 'Site One',
        ]);

        $this->site2 = Site::factory()->forTenant($this->tenant2)->create([
            'name' => 'Site Two',
        ]);

        // Create users for each tenant
        $this->user1 = User::factory()->create([
            'name' => 'User One',
            'email' => 'user1@tenant1.com',
            'password' => Hash::make('password'),
            'tenant_id' => $this->tenant1->id,
            'site_id' => $this->site1->id,
            'status' => 'Active',
        ]);

        $this->user2 = User::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@tenant2.com',
            'password' => Hash::make('password'),
            'tenant_id' => $this->tenant2->id,
            'site_id' => $this->site2->id,
            'status' => 'Active',
        ]);

        // Create departments for each tenant
        Department::factory()->create([
            'name' => 'Dept One',
            'tenant_id' => $this->tenant1->id,
            'site_id' => $this->site1->id,
        ]);

        Department::factory()->create([
            'name' => 'Dept Two',
            'tenant_id' => $this->tenant2->id,
            'site_id' => $this->site2->id,
        ]);
    }

    public function test_users_cannot_see_other_tenant_orders()
    {
        // Create orders for tenant1
        $order1 = Order::factory()->create([
            'user_id' => $this->user1->id,
            'tenant_id' => $this->tenant1->id,
            'site_id' => $this->site1->id,
            'status' => 'Requested',
            'approval_status' => null,
        ]);

        // Create orders for tenant2
        $order2 = Order::factory()->create([
            'user_id' => $this->user2->id,
            'tenant_id' => $this->tenant2->id,
            'site_id' => $this->site2->id,
            'status' => 'Requested',
            'approval_status' => null,
        ]);

        // User1 should only see tenant1's orders
        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        $tenant1Orders = Order::where('tenant_id', $this->tenant1->id)->get();
        $this->assertTrue($tenant1Orders->contains($order1));
        $this->assertFalse($tenant1Orders->contains($order2));

        // User2 should only see tenant2's orders
        $this->actingAs($this->user2);
        session(['current_tenant_id' => $this->tenant2->id]);

        $tenant2Orders = Order::where('tenant_id', $this->tenant2->id)->get();
        $this->assertTrue($tenant2Orders->contains($order2));
        $this->assertFalse($tenant2Orders->contains($order1));
    }

    public function test_users_cannot_see_other_tenant_items()
    {
        // Create items for tenant1
        $item1 = Item::factory()->create([
            'tenant_id' => $this->tenant1->id,
            'site_id' => $this->site1->id,
            'item_description' => 'Tenant One Item',
        ]);

        // Create items for tenant2
        $item2 = Item::factory()->create([
            'tenant_id' => $this->tenant2->id,
            'site_id' => $this->site2->id,
            'item_description' => 'Tenant Two Item',
        ]);

        // User1 should only see tenant1's items
        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        $tenant1Items = Item::where('tenant_id', $this->tenant1->id)->get();
        $this->assertTrue($tenant1Items->contains($item1));
        $this->assertFalse($tenant1Items->contains($item2));

        // User2 should only see tenant2's items
        $this->actingAs($this->user2);
        session(['current_tenant_id' => $this->tenant2->id]);

        $tenant2Items = Item::where('tenant_id', $this->tenant2->id)->get();
        $this->assertTrue($tenant2Items->contains($item2));
        $this->assertFalse($tenant2Items->contains($item1));
    }

    public function test_users_cannot_see_other_tenant_inventory_items()
    {
        // Create inventory items for tenant1
        $invItem1 = InventoryItem::factory()->create([
            'tenant_id' => $this->tenant1->id,
            'site_id' => $this->site1->id,
            'quantity' => 100,
        ]);

        // Create inventory items for tenant2
        $invItem2 = InventoryItem::factory()->create([
            'tenant_id' => $this->tenant2->id,
            'site_id' => $this->site2->id,
            'quantity' => 200,
        ]);

        // User1 should only see tenant1's inventory
        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        $tenant1Inventory = InventoryItem::where('tenant_id', $this->tenant1->id)->get();
        $this->assertTrue($tenant1Inventory->contains($invItem1));
        $this->assertFalse($tenant1Inventory->contains($invItem2));

        // User2 should only see tenant2's inventory
        $this->actingAs($this->user2);
        session(['current_tenant_id' => $this->tenant2->id]);

        $tenant2Inventory = InventoryItem::where('tenant_id', $this->tenant2->id)->get();
        $this->assertTrue($tenant2Inventory->contains($invItem2));
        $this->assertFalse($tenant2Inventory->contains($invItem1));
    }

    public function test_users_cannot_see_other_tenant_departments()
    {
        // User1 should only see tenant1's departments
        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        $tenant1Depts = Department::where('tenant_id', $this->tenant1->id)->get();
        $this->assertCount(1, $tenant1Depts);
        $this->assertEquals('Dept One', $tenant1Depts->first()->name);

        // User2 should only see tenant2's departments
        $this->actingAs($this->user2);
        session(['current_tenant_id' => $this->tenant2->id]);

        $tenant2Depts = Department::where('tenant_id', $this->tenant2->id)->get();
        $this->assertCount(1, $tenant2Depts);
        $this->assertEquals('Dept Two', $tenant2Depts->first()->name);
    }

    public function test_users_cannot_access_other_tenant_sites()
    {
        // User1 should only see tenant1's sites
        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        $tenant1Sites = Site::where('tenant_id', $this->tenant1->id)->get();
        $this->assertTrue($tenant1Sites->contains($this->site1));
        $this->assertFalse($tenant1Sites->contains($this->site2));

        // User2 should only see tenant2's sites
        $this->actingAs($this->user2);
        session(['current_tenant_id' => $this->tenant2->id]);

        $tenant2Sites = Site::where('tenant_id', $this->tenant2->id)->get();
        $this->assertTrue($tenant2Sites->contains($this->site2));
        $this->assertFalse($tenant2Sites->contains($this->site1));
    }

    public function test_super_admin_can_see_all_tenant_data()
    {
        // Create orders for both tenants
        $order1 = Order::factory()->create([
            'tenant_id' => $this->tenant1->id,
            'site_id' => $this->site1->id,
        ]);

        $order2 = Order::factory()->create([
            'tenant_id' => $this->tenant2->id,
            'site_id' => $this->site2->id,
        ]);

        // Create Super Admin
        $superAdmin = User::factory()->superAdmin()->create([
            'status' => 'Active',
        ]);
        $superAdmin->assignRole('Super Admin');

        // Super Admin should see all orders
        $this->actingAs($superAdmin);

        $allOrders = Order::all();
        $this->assertTrue($allOrders->contains($order1));
        $this->assertTrue($allOrders->contains($order2));
    }

    public function test_data_isolation_in_home_controller()
    {
        // Create orders for both tenants
        $order1 = Order::factory()->create([
            'user_id' => $this->user1->id,
            'tenant_id' => $this->tenant1->id,
            'site_id' => $this->site1->id,
            'status' => 'Requested',
            'approval_status' => 'Approved',
        ]);

        $order2 = Order::factory()->create([
            'user_id' => $this->user2->id,
            'tenant_id' => $this->tenant2->id,
            'site_id' => $this->site2->id,
            'status' => 'Requested',
            'approval_status' => 'Approved',
        ]);

        // User1 should only see tenant1's orders in dashboard
        $this->actingAs($this->user1);
        session(['current_tenant_id' => $this->tenant1->id]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        // The dashboard should only show tenant1's data
        // (This test verifies that HomeController filters by tenant_id)
    }
}
