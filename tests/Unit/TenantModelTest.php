<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Site;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TenantModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'Tenant Admin', 'guard_name' => 'web']);
    }

    public function test_tenant_has_many_users()
    {
        $tenant = Tenant::factory()->create();
        $user1 = User::factory()->create(['tenant_id' => $tenant->id]);
        $user2 = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertEquals(2, $tenant->users()->count());
        $this->assertTrue($tenant->users->contains($user1));
        $this->assertTrue($tenant->users->contains($user2));
    }

    public function test_tenant_has_many_sites()
    {
        $tenant = Tenant::factory()->create();
        $site1 = Site::factory()->forTenant($tenant)->create();
        $site2 = Site::factory()->forTenant($tenant)->create();

        $this->assertEquals(2, $tenant->sites()->count());
        $this->assertTrue($tenant->sites->contains($site1));
        $this->assertTrue($tenant->sites->contains($site2));
    }

    public function test_tenant_slug_is_auto_generated()
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Test Company',
            'slug' => null,
        ]);

        $this->assertNotNull($tenant->slug);
        $this->assertEquals('test-company', $tenant->slug);
    }

    public function test_tenant_is_active()
    {
        $activeTenant = Tenant::factory()->create(['status' => 'Active']);
        $inactiveTenant = Tenant::factory()->inactive()->create();

        $this->assertTrue($activeTenant->isActive());
        $this->assertFalse($inactiveTenant->isActive());
    }

    public function test_tenant_has_tenant_admins()
    {
        $tenant = Tenant::factory()->create();
        
        // Create tenant admin (no site_id)
        $tenantAdmin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => null,
        ]);
        $tenantAdmin->assignRole('Tenant Admin');

        // Create regular user (has site_id)
        $site = Site::factory()->forTenant($tenant)->create();
        $regularUser = User::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
        ]);

        $tenantAdmins = $tenant->tenantAdmins()->get();
        $this->assertTrue($tenantAdmins->contains($tenantAdmin));
        $this->assertFalse($tenantAdmins->contains($regularUser));
    }
}
