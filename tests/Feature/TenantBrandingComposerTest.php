<?php

namespace Tests\Feature;

use App\Models\Site;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantBrandingComposerTest extends TestCase
{
    use RefreshDatabase;

    public function test_branding_uses_session_current_tenant_id_when_set(): void
    {
        $tenantA = Tenant::factory()->create(['name' => 'Tenant A', 'logo_path' => 'images/tenants/a.png']);
        $tenantB = Tenant::factory()->create(['name' => 'Tenant B', 'logo_path' => 'images/tenants/b.png']);

        $siteA = Site::factory()->forTenant($tenantA)->create();

        $user = User::factory()->create([
            'site_id' => $siteA->id,
            'tenant_id' => $tenantA->id,
            'status' => 'Active',
        ]);

        $this->actingAs($user);

        // Force tenant context to B (simulates domain routing or future tenant switching).
        session(['current_tenant_id' => $tenantB->id]);

        $html = view('partials.menu')->render();

        $this->assertStringContainsString('Tenant B', $html);
        $this->assertStringContainsString('images/tenants/b.png', $html);
        $this->assertStringNotContainsString('Tenant A', $html);
    }
}

