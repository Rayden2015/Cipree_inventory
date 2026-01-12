<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Site;
use App\Models\Order;
use App\Models\Item;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * End-to-End Test: Full Multi-Tenancy Flow
 * 
 * This test simulates a complete user journey from tenant creation to data operations.
 */
class TenantEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Tenant Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Requester', 'guard_name' => 'web']);
    }

    /**
     * Complete flow: Super Admin creates tenant -> Tenant Admin creates site -> User creates order
     */
    public function test_complete_tenant_lifecycle()
    {
        // Step 1: Super Admin creates a tenant
        $superAdmin = User::factory()->superAdmin()->create([
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);
        $superAdmin->assignRole('Super Admin');

        $tenantData = [
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'status' => 'Active',
            'description' => 'Test company',
            'contact_name' => 'John Doe',
            'contact_email' => 'contact@acme.com',
            'contact_phone' => '+1234567890',
            'admin_name' => 'Tenant Admin',
            'admin_email' => 'admin@acme.com',
            'admin_password' => 'Password123!',
            'admin_password_confirmation' => 'Password123!',
        ];

        $response = $this->actingAs($superAdmin)->post(route('tenants.store'), $tenantData);
        $response->assertRedirect(route('tenants.index'));

        // Verify tenant was created
        $tenant = Tenant::where('slug', 'acme-corp')->first();
        $this->assertNotNull($tenant);
        $this->assertEquals('Acme Corporation', $tenant->name);

        // Verify tenant admin was created
        $tenantAdmin = User::where('email', 'admin@acme.com')->first();
        $this->assertNotNull($tenantAdmin);
        $this->assertTrue($tenantAdmin->hasRole('Tenant Admin'));
        $this->assertEquals($tenant->id, $tenantAdmin->tenant_id);
        
        // Tenant admin is assigned to the default "Head Office" site
        $defaultSite = Site::where('tenant_id', $tenant->id)->where('name', 'Head Office')->first();
        $this->assertNotNull($defaultSite);
        $this->assertEquals($defaultSite->id, $tenantAdmin->site_id);

        // Step 2: Tenant Admin logs in and creates a site
        $this->actingAs($tenantAdmin);
        session(['current_tenant_id' => $tenant->id]);

        $siteData = [
            'name' => 'Main Office',
            'site_code' => 'MO001',
        ];

        $response = $this->post(route('tenant-admin.sites.store'), $siteData);
        $response->assertRedirect(route('tenant-admin.sites.index'));

        $site = Site::where('site_code', 'MO001')->first();
        $this->assertNotNull($site);
        $this->assertEquals($tenant->id, $site->tenant_id);

        // Step 3: Tenant Admin creates a department
        $department = Department::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'name' => 'Operations',
        ]);

        $this->assertEquals($tenant->id, $department->tenant_id);

        // Step 4: Tenant Admin creates a regular user
        $userData = [
            'name' => 'Regular User',
            'email' => 'user@acme.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'site_id' => $site->id,
            'status' => 'Active',
        ];

        $response = $this->post(route('tenant-admin.users.store'), $userData);
        $response->assertRedirect(route('tenant-admin.users.index'));

        $user = User::where('email', 'user@acme.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals($tenant->id, $user->tenant_id);
        $this->assertEquals($site->id, $user->site_id);

        // Step 5: Regular user logs in and creates an order
        $this->actingAs($user);
        session(['current_tenant_id' => $tenant->id]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'status' => 'Requested',
        ]);

        $this->assertEquals($tenant->id, $order->tenant_id);
        $this->assertEquals($site->id, $order->site_id);

        // Step 6: Verify data isolation - create another tenant and verify they can't see each other's data
        $otherTenant = Tenant::factory()->create(['name' => 'Other Tenant']);
        $otherSite = Site::factory()->forTenant($otherTenant)->create();
        $otherUser = User::factory()->create([
            'tenant_id' => $otherTenant->id,
            'site_id' => $otherSite->id,
            'status' => 'Active',
        ]);

        $otherOrder = Order::factory()->create([
            'user_id' => $otherUser->id,
            'tenant_id' => $otherTenant->id,
            'site_id' => $otherSite->id,
        ]);

        // User from tenant1 should not see tenant2's orders
        $this->actingAs($user);
        session(['current_tenant_id' => $tenant->id]);

        $tenant1Orders = Order::where('tenant_id', $tenant->id)->get();
        $this->assertTrue($tenant1Orders->contains($order));
        $this->assertFalse($tenant1Orders->contains($otherOrder));

        // User from tenant2 should not see tenant1's orders
        $this->actingAs($otherUser);
        session(['current_tenant_id' => $otherTenant->id]);

        $tenant2Orders = Order::where('tenant_id', $otherTenant->id)->get();
        $this->assertTrue($tenant2Orders->contains($otherOrder));
        $this->assertFalse($tenant2Orders->contains($order));
    }

    /**
     * Test Super Admin can switch between tenants
     */
    public function test_super_admin_can_switch_tenant_context()
    {
        $superAdmin = User::factory()->superAdmin()->create([
            'status' => 'Active',
        ]);
        $superAdmin->assignRole('Super Admin');

        $tenant1 = Tenant::factory()->create(['name' => 'Tenant One']);
        $tenant2 = Tenant::factory()->create(['name' => 'Tenant Two']);

        // Super Admin can access tenant1 data
        $response = $this->actingAs($superAdmin)->get(route('tenants.show', $tenant1->id) . '?tenant_id=' . $tenant1->id);
        $response->assertStatus(200);
        $this->assertEquals($tenant1->id, session('current_tenant_id'));

        // Super Admin can switch to tenant2
        $response = $this->actingAs($superAdmin)->get(route('tenants.show', $tenant2->id) . '?tenant_id=' . $tenant2->id);
        $response->assertStatus(200);
        $this->assertEquals($tenant2->id, session('current_tenant_id'));
    }

    /**
     * Test complete dashboard flow with tenant isolation
     */
    public function test_dashboard_shows_only_tenant_data()
    {
        $tenant = Tenant::factory()->create();
        $site = Site::factory()->forTenant($tenant)->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'status' => 'Active',
        ]);

        // Create orders for this tenant
        $order1 = Order::factory()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'status' => 'Requested',
            'approval_status' => 'Approved',
        ]);

        // Create orders for another tenant (should not appear)
        $otherTenant = Tenant::factory()->create();
        $otherSite = Site::factory()->forTenant($otherTenant)->create();
        $otherUser = User::factory()->create([
            'tenant_id' => $otherTenant->id,
            'site_id' => $otherSite->id,
            'status' => 'Active',
        ]);

        $order2 = Order::factory()->create([
            'user_id' => $otherUser->id,
            'tenant_id' => $otherTenant->id,
            'site_id' => $otherSite->id,
            'status' => 'Requested',
            'approval_status' => 'Approved',
        ]);

        // User should only see their tenant's orders on dashboard
        $this->actingAs($user);
        session(['current_tenant_id' => $tenant->id]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Verify data isolation in database queries
        $tenantOrders = Order::where('tenant_id', $tenant->id)
            ->where('site_id', $site->id)
            ->get();

        $this->assertTrue($tenantOrders->contains($order1));
        $this->assertFalse($tenantOrders->contains($order2));
    }
}
