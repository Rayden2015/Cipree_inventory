<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Site;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'site_id' => Site::factory(),
            'name' => fake()->company() . ' Department',
            'description' => fake()->sentence(),
        ];
    }
}
