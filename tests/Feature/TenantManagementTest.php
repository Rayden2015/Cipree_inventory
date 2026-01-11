<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class TenantManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        
        // Create roles
        $this->createTenantRoles();
    }

    protected function createTenantRoles(): void
    {
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Tenant Admin', 'guard_name' => 'web']);
    }

    protected function createSuperAdmin(): User
    {
        $user = User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);
        
        $user->assignRole('Super Admin');
        return $user;
    }

    public function test_super_admin_can_view_tenants_list()
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($superAdmin)->get(route('tenants.index'));

        $response->assertStatus(200);
        $response->assertSee($tenant->name);
        $response->assertViewHas('tenants');
    }

    public function test_non_super_admin_cannot_access_tenants_list()
    {
        $user = User::factory()->tenantAdmin()->create([
            'status' => 'Active',
        ]);
        $user->assignRole('Tenant Admin');

        $response = $this->actingAs($user)->get(route('tenants.index'));

        $response->assertRedirect(route('home'));
    }

    public function test_super_admin_can_view_create_tenant_form()
    {
        $superAdmin = $this->createSuperAdmin();

        $response = $this->actingAs($superAdmin)->get(route('tenants.create'));

        $response->assertStatus(200);
        $response->assertSee('Create New Tenant');
    }

    public function test_super_admin_can_create_tenant_with_admin()
    {
        $superAdmin = $this->createSuperAdmin();

        $tenantData = [
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'domain' => 'acme.example.com',
            'status' => 'Active',
            'description' => 'Test tenant description',
            'contact_name' => 'John Doe',
            'contact_email' => 'contact@acme.com',
            'contact_phone' => '+1234567890',
            'admin_name' => 'Tenant Admin',
            'admin_email' => 'admin@acme.com',
            'admin_password' => 'SecurePassword123!',
            'admin_password_confirmation' => 'SecurePassword123!',
        ];

        $response = $this->actingAs($superAdmin)->post(route('tenants.store'), $tenantData);

        // Assert tenant was created
        $this->assertDatabaseHas('tenants', [
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'status' => 'Active',
        ]);

        // Assert tenant admin was created
        $this->assertDatabaseHas('users', [
            'email' => 'admin@acme.com',
            'name' => 'Tenant Admin',
            'status' => 'Active',
        ]);

        $tenantAdmin = User::where('email', 'admin@acme.com')->first();
        $this->assertTrue($tenantAdmin->hasRole('Tenant Admin'));
        $this->assertNotNull($tenantAdmin->tenant_id);

        $response->assertRedirect(route('tenants.index'));
    }

    public function test_tenant_creation_requires_valid_data()
    {
        $superAdmin = $this->createSuperAdmin();

        $response = $this->actingAs($superAdmin)->post(route('tenants.store'), [
            'name' => '',
            'admin_email' => 'invalid-email',
            'admin_password' => 'short',
        ]);

        $response->assertSessionHasErrors(['name', 'admin_email', 'admin_password']);
        $this->assertDatabaseMissing('tenants', ['slug' => '']);
    }

    public function test_super_admin_can_view_tenant_details()
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($superAdmin)->get(route('tenants.show', $tenant->id));

        $response->assertStatus(200);
        $response->assertSee($tenant->name);
        $response->assertViewHas('tenant');
    }

    public function test_super_admin_can_update_tenant()
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create([
            'name' => 'Original Name',
            'status' => 'Active',
        ]);

        $response = $this->actingAs($superAdmin)->put(route('tenants.update', $tenant->id), [
            'name' => 'Updated Name',
            'slug' => $tenant->slug,
            'domain' => $tenant->domain,
            'status' => 'Inactive',
            'description' => 'Updated description',
            'contact_name' => 'Jane Doe',
            'contact_email' => 'jane@example.com',
            'contact_phone' => '+9876543210',
        ]);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Updated Name',
            'status' => 'Inactive',
        ]);

        $response->assertRedirect(route('tenants.index'));
    }

    public function test_super_admin_can_create_additional_tenant_admin()
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($superAdmin)->post(route('tenants.store-admin', $tenant->id), [
            'name' => 'Additional Admin',
            'email' => 'additional@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'additional@example.com',
            'tenant_id' => $tenant->id,
        ]);

        $admin = User::where('email', 'additional@example.com')->first();
        $this->assertTrue($admin->hasRole('Tenant Admin'));

        $response->assertRedirect(route('tenants.show', $tenant->id));
    }

    public function test_super_admin_cannot_delete_tenant_with_sites()
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create();
        $site = Site::factory()->forTenant($tenant)->create();

        $response = $this->actingAs($superAdmin)->delete(route('tenants.destroy', $tenant->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
    }

    public function test_super_admin_can_delete_tenant_without_sites()
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($superAdmin)->delete(route('tenants.destroy', $tenant->id));

        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
        $response->assertRedirect(route('tenants.index'));
    }
}
