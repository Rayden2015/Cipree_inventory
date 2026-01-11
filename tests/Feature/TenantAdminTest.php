<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TenantAdminTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $tenantAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        
        Role::firstOrCreate(['name' => 'Tenant Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $this->tenant = Tenant::factory()->create([
            'name' => 'Test Tenant',
            'status' => 'Active',
        ]);

        $this->tenantAdmin = User::factory()->create([
            'name' => 'Tenant Admin',
            'email' => 'admin@tenant.com',
            'password' => Hash::make('password'),
            'tenant_id' => $this->tenant->id,
            'site_id' => null,
            'status' => 'Active',
        ]);

        $this->tenantAdmin->assignRole('Tenant Admin');
    }

    public function test_tenant_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->tenantAdmin)->get(route('tenant-admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Tenant Admin Dashboard');
        $response->assertViewHas('tenant');
        $response->assertViewHas('stats');
    }

    public function test_tenant_admin_can_view_sites()
    {
        $site = Site::factory()->forTenant($this->tenant)->create();

        $response = $this->actingAs($this->tenantAdmin)->get(route('tenant-admin.sites.index'));

        $response->assertStatus(200);
        $response->assertSee($site->name);
        $response->assertViewHas('sites');
        $response->assertViewHas('tenant');
    }

    public function test_tenant_admin_can_create_site()
    {
        $siteData = [
            'name' => 'New Site',
            'site_code' => 'NS001',
        ];

        $response = $this->actingAs($this->tenantAdmin)->post(route('tenant-admin.sites.store'), $siteData);

        $this->assertDatabaseHas('sites', [
            'name' => 'New Site',
            'site_code' => 'NS001',
            'tenant_id' => $this->tenant->id,
        ]);

        $response->assertRedirect(route('tenant-admin.sites.index'));
    }

    public function test_tenant_admin_cannot_create_site_for_other_tenant()
    {
        $otherTenant = Tenant::factory()->create();
        
        // Try to create site but with wrong tenant context (middleware should prevent this)
        $siteData = [
            'name' => 'Hacked Site',
            'site_code' => 'HACK',
        ];

        // The middleware should ensure tenant context, so this should work correctly
        // and create site for the current tenant, not the other tenant
        $response = $this->actingAs($this->tenantAdmin)->post(route('tenant-admin.sites.store'), $siteData);

        $this->assertDatabaseHas('sites', [
            'name' => 'Hacked Site',
            'tenant_id' => $this->tenant->id, // Should be current tenant's ID
        ]);

        // Verify no site was created for other tenant
        $this->assertDatabaseMissing('sites', [
            'name' => 'Hacked Site',
            'tenant_id' => $otherTenant->id,
        ]);
    }

    public function test_tenant_admin_can_view_users()
    {
        $user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'site_id' => null,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($this->tenantAdmin)->get(route('tenant-admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
        $response->assertViewHas('tenant');
        $response->assertSee($user->name);
    }

    public function test_tenant_admin_can_create_user()
    {
        $site = Site::factory()->forTenant($this->tenant)->create();

        $userData = [
            'name' => 'New User',
            'email' => 'newuser@tenant.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'site_id' => $site->id,
            'status' => 'Active',
        ];

        $response = $this->actingAs($this->tenantAdmin)->post(route('tenant-admin.users.store'), $userData);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@tenant.com',
            'tenant_id' => $this->tenant->id,
            'site_id' => $site->id,
        ]);

        $response->assertRedirect(route('tenant-admin.users.index'));
    }

    public function test_tenant_admin_cannot_create_user_for_other_tenant_site()
    {
        $otherTenant = Tenant::factory()->create();
        $otherSite = Site::factory()->forTenant($otherTenant)->create();

        $userData = [
            'name' => 'Hacked User',
            'email' => 'hacked@tenant.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'site_id' => $otherSite->id, // Try to use other tenant's site
            'status' => 'Active',
        ];

        $response = $this->actingAs($this->tenantAdmin)->post(route('tenant-admin.users.store'), $userData);

        // Should fail validation - site doesn't belong to tenant
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('users', ['email' => 'hacked@tenant.com']);
    }

    public function test_tenant_admin_can_update_tenant_settings()
    {
        $response = $this->actingAs($this->tenantAdmin)->put(route('tenant-admin.update-settings'), [
            'name' => 'Updated Tenant Name',
            'description' => 'Updated description',
            'contact_name' => 'Jane Doe',
            'contact_email' => 'jane@tenant.com',
            'contact_phone' => '+1234567890',
        ]);

        $this->assertDatabaseHas('tenants', [
            'id' => $this->tenant->id,
            'name' => 'Updated Tenant Name',
        ]);

        $response->assertRedirect(route('tenant-admin.settings'));
    }

    public function test_regular_user_cannot_access_tenant_admin_routes()
    {
        $site = Site::factory()->forTenant($this->tenant)->create();
        $user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'site_id' => $site->id,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->get(route('tenant-admin.dashboard'));

        $response->assertRedirect(route('home'));
    }
}
