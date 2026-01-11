<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\Site;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition(): array
    {
        // Create tenant and site first to use in related models
        $tenant = Tenant::factory()->create();
        $site = Site::factory()->forTenant($tenant)->create();
        $item = Item::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
        ]);
        $inventory = Inventory::factory()->create([
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
        ]);
        
        return [
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'inventory_id' => $inventory->id,
            'item_id' => $item->id,
            'quantity' => fake()->numberBetween(1, 1000),
            'amount' => fake()->randomFloat(2, 10, 5000),
        ];
    }
}
