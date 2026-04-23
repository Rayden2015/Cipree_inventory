<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Site;
use App\Models\User;
use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Site $site;
    protected Department $department;
    protected User $admin;
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create tenant first
        $this->tenant = Tenant::factory()->create([
            'name' => 'Test Tenant',
            'status' => 'Active',
        ]);

        // Create site with tenant
        $this->site = Site::factory()->forTenant($this->tenant)->create([
            'name' => 'Primary Site',
            'site_code' => 'PS',
        ]);

        $this->department = Department::create([
            'name' => 'Support',
            'description' => 'Support Department',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->admin = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
        ]);

        $this->grantEmployeePermissions($this->admin, ['add-employee', 'view-employee', 'edit-employee', 'delete-employee']);
    }

    public function test_store_requires_department()
    {
        $payload = [
            'fname' => 'Alice',
            'email' => 'alice@example.com',
            'contract_start_date' => Carbon::now()->subMonth()->toDateString(),
            'duration' => 12,
        ];

        $response = $this->actingAs($this->admin)->post(route('employees.store'), $payload);

        $response->assertSessionHasErrors('department_id');
        $this->assertDatabaseMissing('employees', ['email' => 'alice@example.com']);
    }

    public function test_store_persists_department()
    {
        $payload = [
            'fname' => 'Bob',
            'email' => 'bob@example.com',
            'department_id' => $this->department->id,
            'contract_start_date' => Carbon::now()->subMonth()->toDateString(),
            'duration' => 6,
            'employment_type' => 'Full-time',
            'employee_status' => 'Active',
        ];

        $response = $this->actingAs($this->admin)->post(route('employees.store'), $payload);

        $response->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', [
            'email' => 'bob@example.com',
            'department_id' => $this->department->id,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    protected function grantEmployeePermissions(User $user, array $permissions): void
    {
        $permissionRecords = [];
        foreach ($permissions as $permissionName) {
            $permissionRecords[] = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        $role = Role::create([
            'name' => 'role-' . Str::uuid(),
            'guard_name' => 'web',
        ]);
        $role->givePermissionTo($permissionRecords);
        $user->assignRole($role);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
