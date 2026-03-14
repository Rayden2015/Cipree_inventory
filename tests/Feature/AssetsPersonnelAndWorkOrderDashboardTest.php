<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Enduser;
use App\Models\EndUsersCategory;
use App\Models\Section;
use App\Models\Site;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AssetsPersonnelAndWorkOrderDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Site $site;
    protected User $planner;
    protected User $hrUser;
    protected Enduser $asset;
    protected Enduser $personnel;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['view-asset', 'add-asset', 'edit-asset', 'view-personnel', 'add-personnel', 'edit-personnel', 'view-work-order'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $this->tenant = Tenant::factory()->create(['name' => 'Test Tenant', 'status' => 'Active']);
        $this->site = Site::factory()->forTenant($this->tenant)->create(['name' => 'Main Site', 'site_code' => 'MS1']);

        $dept = Department::create([
            'name' => 'Maintenance',
            'description' => 'Dept',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $section = Section::create([
            'name' => 'Section A',
            'description' => 'Sec',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $category = EndUsersCategory::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->planner = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $dept->id,
            'status' => 'Active',
        ]);
        $this->planner->givePermissionTo(['view-asset', 'view-work-order']);

        $this->hrUser = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $dept->id,
            'status' => 'Active',
        ]);
        $this->hrUser->givePermissionTo(['view-personnel']);

        $this->asset = Enduser::create([
            'name' => 'Excavator 1',
            'name_description' => 'Excavator 1',
            'asset_staff_id' => 'EX-01',
            'type' => 'Equipment',
            'department' => 'Maintenance',
            'section' => 'Section A',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $dept->id,
            'section_id' => $section->id,
            'enduser_category_id' => $category->id,
            'status' => 'Operational',
        ]);

        $this->personnel = Enduser::create([
            'name' => 'Jane Doe',
            'name_description' => 'Jane Doe',
            'asset_staff_id' => 'EMP-01',
            'type' => 'Staff',
            'department' => 'Maintenance',
            'section' => 'Section A',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $dept->id,
            'section_id' => $section->id,
            'enduser_category_id' => $category->id,
            'status' => 'Active',
        ]);
    }

    public function test_assets_list_requires_view_asset_permission(): void
    {
        $response = $this->actingAs($this->planner)->get(route('endusers.assets'));
        $response->assertOk();
        $response->assertSee('Assets');
        $response->assertSee('EX-01');
        $response->assertDontSee('EMP-01');
    }

    public function test_personnel_list_requires_view_personnel_permission(): void
    {
        $response = $this->actingAs($this->hrUser)->get(route('endusers.personnel'));
        $response->assertOk();
        $response->assertSee('Personnel');
        $response->assertSee('EMP-01');
        $response->assertDontSee('EX-01');
    }

    public function test_assets_list_returns_403_without_view_asset(): void
    {
        $user = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'Active',
        ]);
        $user->givePermissionTo(['view-personnel']);
        $response = $this->actingAs($user)->get(route('endusers.assets'));
        $response->assertForbidden();
    }

    public function test_personnel_list_returns_403_without_view_personnel(): void
    {
        $response = $this->actingAs($this->planner)->get(route('endusers.personnel'));
        $response->assertForbidden();
    }

    public function test_show_asset_allowed_with_view_asset(): void
    {
        $response = $this->actingAs($this->planner)->get(route('endusers.show', $this->asset->id));
        $response->assertOk();
        $response->assertSee('EX-01');
    }

    public function test_show_personnel_allowed_with_view_personnel(): void
    {
        $response = $this->actingAs($this->hrUser)->get(route('endusers.show', $this->personnel->id));
        $response->assertOk();
        $response->assertSee('EMP-01');
    }

    public function test_show_asset_denied_with_only_view_personnel(): void
    {
        $response = $this->actingAs($this->hrUser)->get(route('endusers.show', $this->asset->id));
        $response->assertForbidden();
    }

    public function test_show_personnel_denied_with_only_view_asset(): void
    {
        $response = $this->actingAs($this->planner)->get(route('endusers.show', $this->personnel->id));
        $response->assertForbidden();
    }

    public function test_work_order_dashboard_shows_work_orders_in_progress(): void
    {
        WorkOrder::create([
            'work_order_number' => 'WO-TEST-001',
            'title' => 'Repair pump',
            'status' => 'In Progress',
            'priority' => 'High',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->planner)->get(route('work-orders.dashboard'));
        $response->assertOk();
        $response->assertSee('Work Orders In Progress');
        $response->assertSee('Jobs currently in progress');
        $response->assertSee('WO-TEST-001');
        $response->assertDontSee('Critical Queue');
    }

    public function test_work_order_dashboard_empty_in_progress_shows_expected_message(): void
    {
        $response = $this->actingAs($this->planner)->get(route('work-orders.dashboard'));
        $response->assertOk();
        $response->assertSee('Work Orders In Progress');
        $response->assertSee('No work orders in progress.');
    }
}
