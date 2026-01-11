<?php

namespace Database\Factories;

use App\Models\Enduser;
use App\Models\Site;
use App\Models\Tenant;
use App\Models\Department;
use App\Models\Section;
use App\Models\EndUsersCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnduserFactory extends Factory
{
    protected $model = Enduser::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(), // Required by schema
            'name_description' => fake()->name(),
            'asset_staff_id' => 'ASSET-' . strtoupper(fake()->bothify('??####')),
            'type' => fake()->randomElement(['Staff', 'Equipment']),
            'department' => fake()->company() . ' Department',
            'section' => fake()->word(),
            'tenant_id' => Tenant::factory(),
            'site_id' => Site::factory(),
            'department_id' => Department::factory(),
            'section_id' => Section::factory(),
            'enduser_category_id' => EndUsersCategory::factory(),
            'status' => 'Active',
        ];
    }
}
