<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Site;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'site_id' => Site::factory(),
            'user_id' => User::factory(),
            'grn_number' => 'GRN-' . strtoupper(fake()->bothify('??####')),
            'trans_type' => fake()->randomElement(['Purchase', 'Transfer', 'Adjustment']),
            'date' => now(),
        ];
    }
}
