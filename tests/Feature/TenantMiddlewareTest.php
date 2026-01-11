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

class TenantMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Tenant Admin', 'guard_name' => 'web']);
    }

    public function test_middleware_sets_tenant_context_for_regular_user()
    {
        $tenant = Tenant::factory()->create();
        $site = Site::factory()->forTenant($tenant)->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        // Middleware should set tenant context in session
        $this->assertEquals($tenant->id, session('current_tenant_id'));
        $response->assertStatus(200);
    }

    public function test_middleware_allows_super_admin_to_access_all_tenants()
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        $superAdmin = User::factory()->superAdmin()->create([
            'status' => 'Active',
        ]);
        $superAdmin->assignRole('Super Admin');

        // Super Admin should be able to access without tenant restriction
        $response = $this->actingAs($superAdmin)->get(route('home'));

        $response->assertStatus(200);
        // Super Admin can have null tenant context or specific tenant
    }

    public function test_middleware_logs_out_user_without_tenant()
    {
        $user = User::factory()->create([
            'tenant_id' => null,
            'site_id' => null,
            'status' => 'Active',
        ]);

        // User without tenant should be logged out
        $response = $this->actingAs($user)->get(route('home'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_middleware_sets_tenant_from_user_tenant_id()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => null,
            'status' => 'Active',
        ]);
        $user->assignRole('Tenant Admin');

        $response = $this->actingAs($user)->get(route('tenant-admin.dashboard'));

        $this->assertEquals($tenant->id, session('current_tenant_id'));
        $response->assertStatus(200);
    }

    public function test_middleware_sets_tenant_from_user_site()
    {
        $tenant = Tenant::factory()->create();
        $site = Site::factory()->forTenant($tenant)->create();
        $user = User::factory()->create([
            'tenant_id' => null, // No direct tenant_id
            'site_id' => $site->id,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        // Middleware should get tenant from site relationship
        $this->assertEquals($tenant->id, session('current_tenant_id'));
        $response->assertStatus(200);
    }
}
