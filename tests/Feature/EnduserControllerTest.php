<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Site;
use App\Models\User;
use App\Models\Section;
use App\Models\Enduser;
use App\Models\Department;
use App\Models\EndUsersCategory;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class EnduserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Site $site;
    protected Department $department;
    protected Section $section;
    protected EndUsersCategory $category;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->site = Site::create([
            'name' => 'Site One',
            'site_code' => 'S1',
        ]);

        $this->department = Department::create([
            'name' => 'Logistics',
            'description' => 'Logistics Department',
            'site_id' => $this->site->id,
        ]);

        $this->section = Section::create([
            'name' => 'Logistics Section',
            'description' => 'Section Description',
            'site_id' => $this->site->id,
        ]);

        $this->category = EndUsersCategory::create([
            'name' => 'Equipment',
            'description' => 'Default equipment category',
            'site_id' => $this->site->id,
        ]);

        $this->admin = User::factory()->create([
            'site_id' => $this->site->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
        ]);

        $this->assignPermissions($this->admin, ['view-enduser', 'add-enduser', 'edit-enduser']);
    }

    public function test_store_requires_department()
    {
        $payload = [
            'asset_staff_id' => 'ASSET-100',
            'name_description' => 'Forklift',
            'type' => 'Equipment',
            'department' => 'Logistics',
            'section' => 'Main',
            'section_id' => $this->section->id,
            'enduser_category_id' => $this->category->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('endusers.store'), $payload);

        $response->assertSessionHasErrors('department_id');
        $this->assertDatabaseMissing('endusers', ['name_description' => 'Forklift']);
    }

    public function test_store_creates_enduser_with_department()
    {
        $payload = [
            'asset_staff_id' => 'ASSET-101',
            'name_description' => 'Handheld Radio',
            'type' => 'Equipment',
            'department' => 'Logistics',
            'section' => 'Main',
            'section_id' => $this->section->id,
            'department_id' => $this->department->id,
            'enduser_category_id' => $this->category->id,
            'status' => 'Active',
        ];

        $response = $this->actingAs($this->admin)->post(route('endusers.store'), $payload);

        $response->assertRedirect(route('endusers.index'));
        $this->assertDatabaseHas('endusers', [
            'name_description' => 'Handheld Radio',
            'department_id' => $this->department->id,
            'enduser_category_id' => $this->category->id,
        ]);
    }

    public function test_update_requires_department()
    {
        $enduser = Enduser::create([
            'asset_staff_id' => 'ASSET-200',
            'name' => 'Laptop',
            'name_description' => 'Laptop',
            'type' => 'Equipment',
            'department' => 'Logistics',
            'section' => 'North',
            'section_id' => $this->section->id,
            'department_id' => $this->department->id,
            'site_id' => $this->site->id,
            'enduser_category_id' => $this->category->id,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->put(route('endusers.update', $enduser->id), [
            'asset_staff_id' => 'ASSET-200',
            'name_description' => 'Laptop',
            'type' => 'Equipment',
            'department' => 'Logistics',
            'section' => 'North',
            'section_id' => $this->section->id,
            'enduser_category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors('department_id');
    }

    public function test_update_persists_department()
    {
        $enduser = Enduser::create([
            'asset_staff_id' => 'ASSET-300',
            'name' => 'Tablet',
            'name_description' => 'Tablet',
            'type' => 'Equipment',
            'department' => 'Logistics',
            'section' => 'Central',
            'section_id' => $this->section->id,
            'department_id' => $this->department->id,
            'site_id' => $this->site->id,
            'enduser_category_id' => $this->category->id,
            'status' => 'Active',
        ]);

        $newDepartment = Department::create([
            'name' => 'IT',
            'description' => 'IT Department',
            'site_id' => $this->site->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('endusers.update', $enduser->id), [
            'asset_staff_id' => 'ASSET-300',
            'name_description' => 'Tablet',
            'type' => 'Equipment',
            'department' => 'IT',
            'section' => 'Central',
            'section_id' => $this->section->id,
            'department_id' => $newDepartment->id,
            'enduser_category_id' => $this->category->id,
            'site_id' => $this->site->id,
            'status' => 'Active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('endusers', [
            'id' => $enduser->id,
            'department_id' => $newDepartment->id,
        ]);
    }

    protected function assignPermissions(User $user, array $permissions): void
    {
        $permissionModels = [];
        foreach ($permissions as $permissionName) {
            $permissionModels[] = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $role = Role::create([
            'name' => 'enduser-role-' . Str::uuid(),
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo($permissionModels);
        $user->assignRole($role);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
