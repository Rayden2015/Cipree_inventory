<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Enduser;
use App\Models\Site;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class WorkOrderUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'add-work-order', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-work-order', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-work-order', 'guard_name' => 'web']);

        $this->tenant = Tenant::factory()->create([
            'name' => 'WO Tenant',
            'status' => 'Active',
        ]);

        $this->site = Site::factory()->forTenant($this->tenant)->create([
            'name' => 'WO Site',
            'site_code' => 'WOS',
        ]);

        $this->department = Department::create([
            'name' => 'Maintenance',
            'description' => 'Maintenance Department',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->user = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
        ]);

        $this->user->givePermissionTo(['add-work-order', 'edit-work-order', 'view-work-order']);
    }

    public function test_create_work_order_uses_smart_dropdowns_for_asset_and_responsible_person(): void
    {
        // Seed one asset and one person so the selects are populated
        $category = \App\Models\EndUsersCategory::factory()->create([
            'tenant_id' => $this->tenant->id,
            'site_id' => $this->site->id,
        ]);

        Enduser::create([
            'name' => 'Excavator 01',
            'name_description' => 'Excavator 01',
            'asset_staff_id' => 'EQ-01',
            'type' => 'Equipment',
            'department' => 'Maintenance',
            'section' => 'Mining',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'enduser_category_id' => $category->id,
        ]);

        Enduser::create([
            'name' => 'John Tech',
            'name_description' => 'John Tech',
            'asset_staff_id' => 'ST-01',
            'type' => 'Staff',
            'department' => 'Maintenance',
            'section' => 'Mining',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'enduser_category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('work-orders.create'));

        $response->assertStatus(200);

        // Assert the select inputs exist
        $response->assertSee('id="asset_enduser_id"', false);
        $response->assertSee('id="responsible_enduser_id"', false);

        // Assert Select2 is initialized on the dropdowns
        $response->assertSee("$('#asset_enduser_id').select2", false);
        $response->assertSee("$('#responsible_enduser_id').select2", false);

        // Asset state and down-since fields should be present
        $response->assertSee('name="asset_state"', false);
        $response->assertSee('name="asset_down_since"', false);
    }

    public function test_edit_work_order_uses_smart_dropdowns_for_asset_and_responsible_person(): void
    {
        $category = \App\Models\EndUsersCategory::factory()->create([
            'tenant_id' => $this->tenant->id,
            'site_id' => $this->site->id,
        ]);

        $asset = Enduser::create([
            'name' => 'Dump Truck 01',
            'name_description' => 'Dump Truck 01',
            'asset_staff_id' => 'EQ-02',
            'type' => 'Equipment',
            'department' => 'Maintenance',
            'section' => 'Haulage',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'enduser_category_id' => $category->id,
        ]);

        $person = Enduser::create([
            'name' => 'Jane Planner',
            'name_description' => 'Jane Planner',
            'asset_staff_id' => 'ST-02',
            'type' => 'Staff',
            'department' => 'Maintenance',
            'section' => 'Planning',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'enduser_category_id' => $category->id,
        ]);

        $workOrder = \App\Models\WorkOrder::create([
            'work_order_number' => 'WO-WOS-TEST-001',
            'title' => 'Test WO',
            'description' => 'Test description',
            'status' => 'Open',
            'priority' => 'Medium',
            'asset_state' => 'Operational',
            'requested_date' => now(),
            'asset_down_since' => null,
            'asset_enduser_id' => $asset->id,
            'responsible_enduser_id' => $person->id,
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('work-orders.edit', $workOrder));

        $response->assertStatus(200);

        $response->assertSee('id="asset_enduser_id"', false);
        $response->assertSee('id="responsible_enduser_id"', false);

        $response->assertSee("$('#asset_enduser_id').select2", false);
        $response->assertSee("$('#responsible_enduser_id').select2", false);
    }
}

