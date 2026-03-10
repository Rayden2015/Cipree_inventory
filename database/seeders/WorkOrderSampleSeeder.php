<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enduser;
use App\Models\Sorder;
use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderSampleSeeder extends Seeder
{
    /**
     * Seed sample Work Orders:
     *  - Some without any stock requests linked
     *  - Some with existing Sorders referencing their work_order_number
     */
    public function run(): void
    {
        // Use existing data instead of factories to avoid clashes with legacy schemas.
        $asset = Enduser::whereIn('type', ['Equipment', 'Machine'])->first();
        $person = Enduser::whereNotIn('type', ['Equipment', 'Machine'])->first();

        if (! $person || ! $asset) {
            // Not enough data to create meaningful samples; bail out quietly.
            return;
        }

        $site_id = $asset->site_id;
        $tenant_id = $asset->tenant_id;
        $user = User::where('site_id', $site_id)->first() ?? User::first();

        if (! $user) {
            return;
        }

        // 3 standalone work orders (no stock requests)
        WorkOrder::factory()->count(3)->create([
            'status' => 'Open',
            'asset_enduser_id' => $asset->id,
            'responsible_enduser_id' => $person->id,
            'site_id' => $site_id,
            'tenant_id' => $tenant_id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // 2 work orders that will be linked to Sorders (if any exist)
        $linkedWorkOrders = WorkOrder::factory()->count(2)->create([
            'status' => 'In Progress',
            'asset_enduser_id' => $asset->id,
            'responsible_enduser_id' => $person->id,
            'site_id' => $site_id,
            'tenant_id' => $tenant_id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        foreach ($linkedWorkOrders as $index => $workOrder) {
            $query = Sorder::where('site_id', $workOrder->site_id)
                ->where('tenant_id', $workOrder->tenant_id)
                ->whereNull('work_order_number');

            // For the first linked work order, attach MANY requests (no hard limit)
            // to clearly demonstrate the one-to-many relationship.
            if ($index === 0) {
                $query->update(['work_order_number' => $workOrder->work_order_number]);
            } else {
                // For the second, just attach a couple for variety.
                $query->limit(2)->update(['work_order_number' => $workOrder->work_order_number]);
            }
        }
    }
}

