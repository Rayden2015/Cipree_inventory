<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Site;
use App\Models\Category;
use App\Models\Department;
use App\Models\Section;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class MasterDataControllersTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Tenant $tenant2;
    protected Site $site;
    protected Site $site2;
    protected User $tenantAdmin;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create roles
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Tenant Admin', 'guard_name' => 'web']);

        // Create permissions
        Permission::firstOrCreate(['name' => 'view-item-group', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'add-item-group', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-item-group', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-department', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'add-department', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-department', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-section', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'add-section', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-section', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-location', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'add-location', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-location', 'guard_name' => 'web']);

        // Create tenants
        $this->tenant = Tenant::factory()->create(['name' => 'Tenant One', 'status' => 'Active']);
        $this->tenant2 = Tenant::factory()->create(['name' => 'Tenant Two', 'status' => 'Active']);

        // Create sites
        $this->site = Site::factory()->create([
            'name' => 'Site One',
            'site_code' => 'S1',
            'tenant_id' => $this->tenant->id,
        ]);
        $this->site2 = Site::factory()->create([
            'name' => 'Site Two',
            'site_code' => 'S2',
            'tenant_id' => $this->tenant2->id,
        ]);

        // Create tenant admin user
        $this->tenantAdmin = User::factory()->create([
            'name' => 'Tenant Admin',
            'email' => 'tenantadmin@test.com',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'Active',
        ]);
        $this->tenantAdmin->assignRole('Tenant Admin');
        $this->tenantAdmin->givePermissionTo([
            'view-item-group', 'add-item-group', 'edit-item-group',
            'view-department', 'add-department', 'edit-department',
            'view-section', 'add-section', 'edit-section',
            'view-location', 'add-location', 'edit-location',
        ]);

        // Create super admin user
        $this->superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'status' => 'Active',
        ]);
        $this->superAdmin->assignRole('Super Admin');
    }

    // ==================== CATEGORY CONTROLLER TESTS ====================

    public function test_category_index_displays_categories()
    {
        Category::factory()->create([
            'name' => 'Test Category',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Category');
    }

    public function test_category_index_search_functionality()
    {
        Category::factory()->create([
            'name' => 'Electronics',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
        Category::factory()->create([
            'name' => 'Furniture',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->get(route('categories.index', ['search' => 'Electronics']));

        $response->assertStatus(200);
        $response->assertSee('Electronics');
        $response->assertDontSee('Furniture');
    }

    public function test_category_store_creates_category_with_tenant_id()
    {
        $payload = [
            'name' => 'New Category',
            'description' => 'Test Description',
        ];

        $response = $this->actingAs($this->tenantAdmin)
            ->post(route('categories.store'), $payload);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'description' => 'Test Description',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_category_store_requires_name()
    {
        $payload = ['description' => 'Test Description'];

        $response = $this->actingAs($this->tenantAdmin)
            ->post(route('categories.store'), $payload);

        // Validation should prevent creation
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('categories', ['description' => 'Test Description']);
    }

    public function test_category_update_updates_category()
    {
        $category = Category::factory()->create([
            'name' => 'Old Name',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $payload = [
            'name' => 'New Name',
            'description' => 'Updated Description',
        ];

        $response = $this->actingAs($this->tenantAdmin)
            ->put(route('categories.update', $category->id), $payload);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'description' => 'Updated Description',
        ]);
    }

    public function test_category_destroy_deletes_category()
    {
        $category = Category::factory()->create([
            'name' => 'To Delete',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->delete(route('categories.destroy', $category->id));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_tenant_isolation()
    {
        Category::factory()->create([
            'name' => 'Tenant 1 Category',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
        Category::factory()->create([
            'name' => 'Tenant 2 Category',
            'site_id' => $this->site2->id,
            'tenant_id' => $this->tenant2->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Tenant 1 Category');
        $response->assertDontSee('Tenant 2 Category');
    }

    // ==================== DEPARTMENT CONTROLLER TESTS ====================

    public function test_department_index_displays_departments()
    {
        Department::factory()->create([
            'name' => 'HR Department',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)->get(route('departmentslist.index'));

        $response->assertStatus(200);
        $response->assertSee('HR Department');
    }

    public function test_department_index_search_functionality()
    {
        Department::factory()->create([
            'name' => 'Engineering',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
        Department::factory()->create([
            'name' => 'Sales',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->get(route('departmentslist.index', ['search' => 'Engineering']));

        $response->assertStatus(200);
        $response->assertSee('Engineering');
        $response->assertDontSee('Sales');
    }

    public function test_department_store_creates_department_with_tenant_id()
    {
        $payload = [
            'name' => 'New Department',
            'description' => 'Department Description',
        ];

        $response = $this->actingAs($this->tenantAdmin)
            ->post(route('departmentslist.store'), $payload);

        $response->assertRedirect(route('departmentslist.index'));
        $this->assertDatabaseHas('departments', [
            'name' => 'New Department',
            'description' => 'Department Description',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_department_store_requires_name()
    {
        $payload = ['description' => 'Test Description'];

        $response = $this->actingAs($this->tenantAdmin)
            ->post(route('departmentslist.store'), $payload);

        $response->assertSessionHasErrors('name');
    }

    public function test_department_update_updates_department()
    {
        $department = Department::factory()->create([
            'name' => 'Old Department',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $payload = [
            'name' => 'Updated Department',
            'description' => 'Updated Description',
        ];

        $response = $this->actingAs($this->tenantAdmin)
            ->put(route('departmentslist.update', $department->id), $payload);

        $response->assertRedirect(route('departmentslist.index'));
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Updated Department',
        ]);
    }

    public function test_department_destroy_deletes_department()
    {
        $department = Department::factory()->create([
            'name' => 'To Delete',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->delete(route('departmentslist.destroy', $department->id));

        $response->assertRedirect(route('departmentslist.index'));
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_department_tenant_isolation()
    {
        Department::factory()->create([
            'name' => 'Tenant 1 Department',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
        Department::factory()->create([
            'name' => 'Tenant 2 Department',
            'site_id' => $this->site2->id,
            'tenant_id' => $this->tenant2->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->get(route('departmentslist.index'));

        $response->assertStatus(200);
        $response->assertSee('Tenant 1 Department');
        $response->assertDontSee('Tenant 2 Department');
    }

    // ==================== SECTION CONTROLLER TESTS ====================

    public function test_section_index_displays_sections()
    {
        Section::factory()->create([
            'name' => 'Section A',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)->get(route('sectionslist.index'));

        $response->assertStatus(200);
        $response->assertSee('Section A');
    }

    public function test_section_index_search_functionality()
    {
        Section::factory()->create([
            'name' => 'Marketing Section',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
        Section::factory()->create([
            'name' => 'Finance Section',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->get(route('sectionslist.index', ['search' => 'Marketing']));

        $response->assertStatus(200);
        $response->assertSee('Marketing Section');
        $response->assertDontSee('Finance Section');
    }

    public function test_section_store_creates_section_with_tenant_id()
    {
        $payload = [
            'name' => 'New Section',
            'description' => 'Section Description',
        ];

        $response = $this->actingAs($this->tenantAdmin)
            ->post(route('sectionslist.store'), $payload);

        $response->assertRedirect(route('sectionslist.index'));
        $this->assertDatabaseHas('sections', [
            'name' => 'New Section',
            'description' => 'Section Description',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_section_store_requires_name()
    {
        $payload = ['description' => 'Test Description'];

        $response = $this->actingAs($this->tenantAdmin)
            ->post(route('sectionslist.store'), $payload);

        $response->assertSessionHasErrors('name');
    }

    public function test_section_update_updates_section()
    {
        $section = Section::factory()->create([
            'name' => 'Old Section',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $payload = [
            'name' => 'Updated Section',
            'description' => 'Updated Description',
        ];

        $response = $this->actingAs($this->tenantAdmin)
            ->put(route('sectionslist.update', $section->id), $payload);

        $response->assertRedirect(route('sectionslist.index'));
        $this->assertDatabaseHas('sections', [
            'id' => $section->id,
            'name' => 'Updated Section',
        ]);
    }

    public function test_section_destroy_deletes_section()
    {
        $section = Section::factory()->create([
            'name' => 'To Delete',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->delete(route('sectionslist.destroy', $section->id));

        $response->assertRedirect(route('sectionslist.index'));
        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }

    public function test_section_tenant_isolation()
    {
        Section::factory()->create([
            'name' => 'Tenant 1 Section',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
        Section::factory()->create([
            'name' => 'Tenant 2 Section',
            'site_id' => $this->site2->id,
            'tenant_id' => $this->tenant2->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->get(route('sectionslist.index'));

        $response->assertStatus(200);
        $response->assertSee('Tenant 1 Section');
        $response->assertDontSee('Tenant 2 Section');
    }

    // ==================== LOCATION CONTROLLER TESTS ====================

    public function test_location_index_displays_locations()
    {
        Location::factory()->create([
            'name' => 'Main Warehouse',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)->get(route('locations.index'));

        $response->assertStatus(200);
        $response->assertSee('Main Warehouse');
    }

    public function test_location_index_search_functionality()
    {
        Location::factory()->create([
            'name' => 'Warehouse A',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
        Location::factory()->create([
            'name' => 'Warehouse B',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->get(route('locations.index', ['search' => 'Warehouse A']));

        $response->assertStatus(200);
        $response->assertSee('Warehouse A');
        $response->assertDontSee('Warehouse B');
    }

    public function test_location_store_creates_location_with_tenant_id()
    {
        $payload = [
            'name' => 'New Location',
            'description' => 'Location Description',
        ];

        $response = $this->actingAs($this->tenantAdmin)
            ->post(route('locations.store'), $payload);

        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('locations', [
            'name' => 'New Location',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_location_store_requires_name()
    {
        $payload = ['description' => 'Test Description'];

        $response = $this->actingAs($this->tenantAdmin)
            ->post(route('locations.store'), $payload);

        $response->assertSessionHasErrors('name');
    }

    public function test_location_update_updates_location()
    {
        $location = Location::factory()->create([
            'name' => 'Old Location',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $payload = [
            'name' => 'Updated Location',
            'description' => 'Updated Description',
        ];

        $response = $this->actingAs($this->tenantAdmin)
            ->put(route('locations.update', $location->id), $payload);

        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Updated Location',
        ]);
    }

    public function test_location_destroy_deletes_location()
    {
        $location = Location::factory()->create([
            'name' => 'To Delete',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->delete(route('locations.destroy', $location->id));

        $response->assertRedirect(route('locations.index'));
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    public function test_location_tenant_isolation()
    {
        Location::factory()->create([
            'name' => 'Tenant 1 Location',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
        Location::factory()->create([
            'name' => 'Tenant 2 Location',
            'site_id' => $this->site2->id,
            'tenant_id' => $this->tenant2->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->get(route('locations.index'));

        $response->assertStatus(200);
        $response->assertSee('Tenant 1 Location');
        $response->assertDontSee('Tenant 2 Location');
    }
}
