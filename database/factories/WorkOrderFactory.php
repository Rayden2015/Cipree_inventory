<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        return [
            'work_order_number' => 'WO-' . $this->faker->unique()->numberBetween(1000, 9999),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['Open', 'In Progress', 'Completed']),
            'priority' => $this->faker->randomElement(['Low', 'Medium', 'High', 'Critical']),
            'requested_date' => now()->subDays($this->faker->numberBetween(0, 5)),
            'due_date' => now()->addDays($this->faker->numberBetween(1, 10)),
            'completed_date' => null,
            // The caller is responsible for providing tenant, site, asset, person and user IDs.
            'asset_enduser_id' => null,
            'responsible_enduser_id' => null,
            'site_id' => null,
            'tenant_id' => null,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

