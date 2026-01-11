<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Site;
use App\Models\Tenant;
use App\Models\Supplier;
use App\Models\Enduser;
use App\Models\Department;
use App\Models\Section;
use App\Models\EndUsersCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // Create tenant and site first to use in related models
        $tenant = Tenant::factory()->create();
        $site = Site::factory()->forTenant($tenant)->create();
        
        // Create supplier with same tenant/site
        $supplier = Supplier::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
        ]);
        
        // Create department, section, category for enduser
        $department = Department::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
        ]);
        $section = Section::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
        ]);
        $category = EndUsersCategory::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
        ]);
        
        // Create enduser with same tenant/site
        $enduser = Enduser::factory()->create([
            'name' => fake()->name(),
            'name_description' => fake()->name(),
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'department_id' => $department->id,
            'section_id' => $section->id,
            'enduser_category_id' => $category->id,
        ]);
        
        return [
            'user_id' => User::factory()->create([
                'tenant_id' => $tenant->id,
                'site_id' => $site->id,
            ])->id,
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'supplier_id' => $supplier->id,
            'enduser_id' => $enduser->id,
            'type_of_purchase' => 'Direct Purchase',
            'status' => 'Requested',
            'approval_status' => null,
            'request_number' => 'REQ-' . strtoupper(fake()->bothify('??####')),
            'request_date' => now(),
            'currency' => 'USD',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => 'Approved',
            'approved_by' => User::factory(),
            'approved_on' => now(),
        ]);
    }
}
