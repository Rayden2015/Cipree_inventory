<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Tenant Admin', 'guard_name' => 'web']);
    }

    public function test_user_has_tenant_relationship()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertNotNull($user->tenant);
        $this->assertEquals($tenant->id, $user->tenant->id);
    }

    public function test_get_current_tenant_from_tenant_id()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => null,
        ]);

        $currentTenant = $user->getCurrentTenant();
        $this->assertNotNull($currentTenant);
        $this->assertEquals($tenant->id, $currentTenant->id);
    }

    public function test_get_current_tenant_from_site()
    {
        $tenant = Tenant::factory()->create();
        $site = Site::factory()->forTenant($tenant)->create();
        $user = User::factory()->create([
            'tenant_id' => null,
            'site_id' => $site->id,
        ]);

        $currentTenant = $user->getCurrentTenant();
        $this->assertNotNull($currentTenant);
        $this->assertEquals($tenant->id, $currentTenant->id);
    }

    public function test_user_is_super_admin()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $superAdmin->assignRole('Super Admin');

        $regularUser = User::factory()->create();
        
        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertFalse($regularUser->isSuperAdmin());
    }

    public function test_user_is_tenant_admin()
    {
        $tenant = Tenant::factory()->create();
        $tenantAdmin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => null,
        ]);
        $tenantAdmin->assignRole('Tenant Admin');

        $regularUser = User::factory()->create();
        
        $this->assertTrue($tenantAdmin->isTenantAdmin());
        $this->assertFalse($regularUser->isTenantAdmin());
    }
}
