<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Site;
use App\Models\User;
use App\Models\Section;
use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Site $site;
    protected Department $department;
    protected Section $section;
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
            'name' => 'Main Site',
            'site_code' => 'MS',
        ]);

        $this->department = Department::create([
            'name' => 'Operations',
            'description' => 'Ops Department',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $this->section = Section::create([
            'name' => 'Section A',
            'description' => 'Section Description',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_store_requires_department()
    {
        Mail::fake();
        $admin = $this->makeAdminUser(['add-user', 'view-user']);

        $payload = [
            'name' => 'Validation User',
            'email' => 'validation@example.com',
            'site_id' => $this->site->id,
            'status' => 'Active',
        ];

        $response = $this->actingAs($admin)->post(route('users.store'), $payload);

        $response->assertSessionHasErrors('department_id');
        $this->assertDatabaseMissing('users', ['email' => 'validation@example.com']);
    }

    public function test_store_persists_department()
    {
        Mail::fake();
        $admin = $this->makeAdminUser(['add-user', 'view-user']);

        $email = 'newuser@example.com';

        $payload = [
            'name' => 'New User',
            'email' => $email,
            'site_id' => $this->site->id,
            'department_id' => $this->department->id,
            'section_id' => $this->section->id,
            'status' => 'Active',
            'phone' => null,
            'roles' => [],
        ];

        $response = $this->actingAs($admin)->post(route('users.store'), $payload);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'department_id' => $this->department->id,
        ]);
        Mail::assertSent(WelcomeMail::class);
    }

    public function test_update_requires_department()
    {
        $admin = $this->makeAdminUser(['edit-user']);

        $target = User::factory()->create([
            'email' => 'target@example.com',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
        ]);

        $response = $this->actingAs($admin)->put(route('users.update', $target->id), [
            'name' => 'Updated Name',
            'email' => 'target@example.com',
            'status' => 'Active',
            'site_id' => $this->site->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'department_id' => $this->department->id,
        ]);
    }

    public function test_update_persists_department()
    {
        $admin = $this->makeAdminUser(['edit-user', 'view-user']);

        $target = User::factory()->create([
            'email' => 'update-me@example.com',
            'department_id' => $this->department->id,
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'status' => 'Active',
        ]);

        $newDepartment = Department::create([
            'name' => 'Finance',
            'description' => 'Finance Department',
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($admin)->put(route('users.update', $target->id), [
            'name' => 'Updated User',
            'email' => 'update-me@example.com',
            'status' => 'Active',
            'site_id' => $this->site->id,
            'department_id' => $newDepartment->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'department_id' => $newDepartment->id,
        ]);
    }

    protected function makeAdminUser(array $permissions): User
    {
        $user = User::factory()->create([
            'site_id' => $this->site->id,
            'tenant_id' => $this->tenant->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
        ]);

        $permissionRecords = [];
        foreach ($permissions as $permissionName) {
            $permissionRecords[] = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        if (! empty($permissionRecords)) {
            $role = Role::create([
                'name' => 'role-' . Str::uuid(),
                'guard_name' => 'web',
            ]);

            $role->givePermissionTo($permissionRecords);
            $user->assignRole($role);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }
}
