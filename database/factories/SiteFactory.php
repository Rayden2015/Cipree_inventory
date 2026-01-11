<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Site>
 */
class SiteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Site::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Site',
            'site_code' => strtoupper(fake()->unique()->bothify('??###')),
            'tenant_id' => Tenant::factory(),
        ];
    }

    /**
     * Indicate that the site belongs to a specific tenant.
     */
    public function forTenant($tenant): static
    {
        $tenantId = is_object($tenant) ? $tenant->id : $tenant;
        
        return $this->state(function (array $attributes) use ($tenantId) {
            return [
                'tenant_id' => $tenantId,
            ];
        });
    }
}
