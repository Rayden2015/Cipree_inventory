<?php

namespace Database\Factories;

use App\Models\EndUsersCategory;
use App\Models\Site;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class EndUsersCategoryFactory extends Factory
{
    protected $model = EndUsersCategory::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'site_id' => Site::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }
}
