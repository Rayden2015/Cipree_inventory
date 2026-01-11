<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Site;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'site_id' => Site::factory(),
            'item_description' => fake()->words(3, true),
            'item_stock_code' => strtoupper(fake()->bothify('??####')),
            'item_part_number' => strtoupper(fake()->bothify('PN-####')),
            'stock_quantity' => fake()->numberBetween(0, 1000),
            'amount' => fake()->randomFloat(2, 10, 1000),
        ];
    }
}
