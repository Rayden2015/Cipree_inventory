<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Site;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Site $site;
    protected Department $department;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->site = Site::create([
            'name' => 'Primary Site',
            'site_code' => 'PS',
        ]);

        $this->department = Department::create([
            'name' => 'Support',
            'description' => 'Support Department',
            'site_id' => $this->site->id,
        ]);

        $this->admin = User::factory()->create([
            'site_id' => $this->site->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
        ]);
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
        ]);
    }
}
