<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Site;
use App\Models\Item;
use App\Models\Inventory;
use App\Models\Department;
use App\Models\Category;
use App\Models\Location;
use App\Models\Part;
use App\Models\Employee;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TenantScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        
        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Tenant Admin', 'guard_name' => 'web']);
    }

    /**
     * Test that Super Admin can see all tenants' data
     */
    public function test_super_admin_can_see_all_tenants_data()
    {
        // Create two tenants
        $tenant1 = Tenant::factory()->create(['name' => 'Tenant 1']);
        $tenant2 = Tenant::factory()->create(['name' => 'Tenant 2']);

        // Create sites for each tenant
        $site1 = Site::factory()->create(['tenant_id' => $tenant1->id]);
        $site2 = Site::factory()->create(['tenant_id' => $tenant2->id]);

        // Create categories for each tenant
        $category1 = Category::factory()->create([
            'name' => 'Category 1',
            'site_id' => $site1->id,
            'tenant_id' => $tenant1->id,
        ]);
        $category2 = Category::factory()->create([
            'name' => 'Category 2',
            'site_id' => $site2->id,
            'tenant_id' => $tenant2->id,
        ]);

        // Create users for added_by field
        $user1 = User::factory()->create(['tenant_id' => $tenant1->id, 'site_id' => $site1->id]);
        $user2 = User::factory()->create(['tenant_id' => $tenant2->id, 'site_id' => $site2->id]);

        // Create items for each tenant (bypass scope to set tenant_id directly)
        Item::withoutTenantScope()->create([
            'item_description' => 'Item for Tenant 1',
            'item_stock_code' => 'T1-001',
            'item_part_number' => 'PART-T1-001',
            'site_id' => $site1->id,
            'tenant_id' => $tenant1->id,
            'item_category_id' => $category1->id,
            'added_by' => $user1->id,
        ]);

        Item::withoutTenantScope()->create([
            'item_description' => 'Item for Tenant 2',
            'item_stock_code' => 'T2-001',
            'item_part_number' => 'PART-T2-001',
            'site_id' => $site2->id,
            'tenant_id' => $tenant2->id,
            'item_category_id' => $category2->id,
            'added_by' => $user2->id,
        ]);

        // Create Super Admin user
        $superAdmin = User::factory()->superAdmin()->create([
            'email' => 'superadmin@test.com',
        ]);
        $superAdmin->assignRole('Super Admin');

        // Act as Super Admin
        $this->actingAs($superAdmin);

        // Assert Super Admin can see all items
        $items = Item::all();
        $this->assertGreaterThanOrEqual(2, $items->count(), 'Super Admin should see all tenants\' items');
    }

    /**
     * Test that Tenant Admin only sees their tenant's data
     */
    public function test_tenant_admin_only_sees_own_tenant_data()
    {
        // Create two tenants
        $tenant1 = Tenant::factory()->create(['name' => 'Tenant 1']);
        $tenant2 = Tenant::factory()->create(['name' => 'Tenant 2']);

        // Create sites for each tenant
        $site1 = Site::factory()->create(['tenant_id' => $tenant1->id]);
        $site2 = Site::factory()->create(['tenant_id' => $tenant2->id]);

        // Create categories for each tenant
        $category1 = Category::factory()->create([
            'name' => 'Category 1',
            'site_id' => $site1->id,
            'tenant_id' => $tenant1->id,
        ]);
        $category2 = Category::factory()->create([
            'name' => 'Category 2',
            'site_id' => $site2->id,
            'tenant_id' => $tenant2->id,
        ]);

        // Create users for added_by field
        $user1 = User::factory()->create(['tenant_id' => $tenant1->id, 'site_id' => $site1->id]);
        $user2 = User::factory()->create(['tenant_id' => $tenant2->id, 'site_id' => $site2->id]);

        // Create items for each tenant (bypass scope to set tenant_id directly)
        Item::withoutTenantScope()->create([
            'item_description' => 'Item for Tenant 1',
            'item_stock_code' => 'T1-001',
            'item_part_number' => 'PART-T1-001',
            'site_id' => $site1->id,
            'tenant_id' => $tenant1->id,
            'item_category_id' => $category1->id,
            'added_by' => $user1->id,
        ]);

        Item::withoutTenantScope()->create([
            'item_description' => 'Item for Tenant 2',
            'item_stock_code' => 'T2-001',
            'item_part_number' => 'PART-T2-001',
            'site_id' => $site2->id,
            'tenant_id' => $tenant2->id,
            'item_category_id' => $category2->id,
            'added_by' => $user2->id,
        ]);

        // Create Tenant Admin for Tenant 1
        $tenantAdmin = User::factory()->create([
            'email' => 'admin@tenant1.com',
            'tenant_id' => $tenant1->id,
            'site_id' => $site1->id,
        ]);
        $tenantAdmin->assignRole('Tenant Admin');

        // Act as Tenant Admin (actingAs sets up session properly)
        $this->actingAs($tenantAdmin);
        session(['current_tenant_id' => $tenant1->id]);

        // Assert Tenant Admin only sees their tenant's items
        $items = Item::all();
        $this->assertEquals(1, $items->count(), 'Tenant Admin should only see their tenant\'s items');
        $this->assertEquals('Item for Tenant 1', $items->first()->item_description);
    }

    /**
     * Test that scope works for Inventory model
     */
    public function test_inventory_scope_works()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        $site1 = Site::factory()->create(['tenant_id' => $tenant1->id]);
        $site2 = Site::factory()->create(['tenant_id' => $tenant2->id]);

        Inventory::withoutTenantScope()->create([
            'waybill' => 'WB-001',
            'site_id' => $site1->id,
            'tenant_id' => $tenant1->id,
            'date' => now(),
        ]);

        Inventory::withoutTenantScope()->create([
            'waybill' => 'WB-002',
            'site_id' => $site2->id,
            'tenant_id' => $tenant2->id,
            'date' => now(),
        ]);

        $tenantAdmin = User::factory()->create([
            'tenant_id' => $tenant1->id,
            'site_id' => $site1->id,
        ]);
        $tenantAdmin->assignRole('Tenant Admin');

        // Act as Tenant Admin (actingAs sets up session properly)
        $this->actingAs($tenantAdmin);
        session(['current_tenant_id' => $tenant1->id]);

        $inventories = Inventory::all();
        $this->assertEquals(1, $inventories->count());
        $this->assertEquals('WB-001', $inventories->first()->waybill);
    }

    /**
     * Test that scope works for Department model
     */
    public function test_department_scope_works()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        $site1 = Site::factory()->create(['tenant_id' => $tenant1->id]);
        $site2 = Site::factory()->create(['tenant_id' => $tenant2->id]);

        Department::withoutTenantScope()->create([
            'name' => 'Dept 1',
            'site_id' => $site1->id,
            'tenant_id' => $tenant1->id,
        ]);

        Department::withoutTenantScope()->create([
            'name' => 'Dept 2',
            'site_id' => $site2->id,
            'tenant_id' => $tenant2->id,
        ]);

        $tenantAdmin = User::factory()->create([
            'tenant_id' => $tenant1->id,
            'site_id' => $site1->id,
        ]);
        $tenantAdmin->assignRole('Tenant Admin');

        // Act as Tenant Admin (actingAs sets up session properly)
        $this->actingAs($tenantAdmin);
        session(['current_tenant_id' => $tenant1->id]);

        $departments = Department::all();
        $this->assertEquals(1, $departments->count());
        $this->assertEquals('Dept 1', $departments->first()->name);
    }

    /**
     * Test that scope works for Category model
     */
    public function test_category_scope_works()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        $site1 = Site::factory()->create(['tenant_id' => $tenant1->id]);
        $site2 = Site::factory()->create(['tenant_id' => $tenant2->id]);

        Category::withoutTenantScope()->create([
            'name' => 'Category 1',
            'site_id' => $site1->id,
            'tenant_id' => $tenant1->id,
        ]);

        Category::withoutTenantScope()->create([
            'name' => 'Category 2',
            'site_id' => $site2->id,
            'tenant_id' => $tenant2->id,
        ]);

        $tenantAdmin = User::factory()->create([
            'tenant_id' => $tenant1->id,
            'site_id' => $site1->id,
        ]);
        $tenantAdmin->assignRole('Tenant Admin');

        // Act as Tenant Admin (actingAs sets up session properly)
        $this->actingAs($tenantAdmin);
        session(['current_tenant_id' => $tenant1->id]);

        $categories = Category::all();
        $this->assertEquals(1, $categories->count());
        $this->assertEquals('Category 1', $categories->first()->name);
    }

    /**
     * Test that withoutTenantScope() bypasses the scope
     */
    public function test_without_tenant_scope_bypasses_filtering()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        $site1 = Site::factory()->create(['tenant_id' => $tenant1->id]);
        $site2 = Site::factory()->create(['tenant_id' => $tenant2->id]);

        // Create categories
        $category1 = Category::factory()->create([
            'name' => 'Category 1',
            'site_id' => $site1->id,
            'tenant_id' => $tenant1->id,
        ]);
        $category2 = Category::factory()->create([
            'name' => 'Category 2',
            'site_id' => $site2->id,
            'tenant_id' => $tenant2->id,
        ]);

        // Create users
        $user1 = User::factory()->create(['tenant_id' => $tenant1->id, 'site_id' => $site1->id]);
        $user2 = User::factory()->create(['tenant_id' => $tenant2->id, 'site_id' => $site2->id]);

        Item::withoutTenantScope()->create([
            'item_description' => 'Item 1',
            'item_stock_code' => 'T1-001',
            'item_part_number' => 'PART-001',
            'site_id' => $site1->id,
            'tenant_id' => $tenant1->id,
            'item_category_id' => $category1->id,
            'added_by' => $user1->id,
        ]);

        Item::withoutTenantScope()->create([
            'item_description' => 'Item 2',
            'item_stock_code' => 'T2-001',
            'item_part_number' => 'PART-002',
            'site_id' => $site2->id,
            'tenant_id' => $tenant2->id,
            'item_category_id' => $category2->id,
            'added_by' => $user2->id,
        ]);

        $tenantAdmin = User::factory()->create([
            'tenant_id' => $tenant1->id,
            'site_id' => $site1->id,
        ]);
        $tenantAdmin->assignRole('Tenant Admin');

        // Act as Tenant Admin (actingAs sets up session properly)
        $this->actingAs($tenantAdmin);
        session(['current_tenant_id' => $tenant1->id]);

        // With scope (default) - should see only tenant1's items
        $scopedItems = Item::all();
        $this->assertEquals(1, $scopedItems->count());

        // Without scope - should see all items
        $allItems = Item::withoutTenantScope()->get();
        $this->assertGreaterThanOrEqual(2, $allItems->count());
    }
}
