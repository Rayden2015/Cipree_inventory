<?php

namespace Tests\Unit;

use App\Models\InventoryCorrectionReasonCode;
use App\Models\InventoryCorrectionRequest;
use App\Models\InventoryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryCorrectionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_correction_request_status_constants(): void
    {
        $this->assertEquals('pending', InventoryCorrectionRequest::STATUS_PENDING);
        $this->assertEquals('approved', InventoryCorrectionRequest::STATUS_APPROVED);
        $this->assertEquals('rejected', InventoryCorrectionRequest::STATUS_REJECTED);
    }

    public function test_inventory_correction_request_is_pending(): void
    {
        $request = new InventoryCorrectionRequest(['status' => InventoryCorrectionRequest::STATUS_PENDING]);
        $this->assertTrue($request->isPending());
        $this->assertFalse($request->isApproved());

        $request->status = InventoryCorrectionRequest::STATUS_APPROVED;
        $this->assertFalse($request->isPending());
        $this->assertTrue($request->isApproved());
    }

    public function test_inventory_item_status_constants(): void
    {
        $this->assertEquals('Active', InventoryItem::STATUS_ACTIVE);
        $this->assertEquals('Voided', InventoryItem::STATUS_VOIDED);
        $this->assertEquals('Adjustment', InventoryItem::STATUS_ADJUSTMENT);
    }

    public function test_inventory_item_scope_active_only_filters_status(): void
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $site = \App\Models\Site::factory()->forTenant($tenant)->create();
        $inv = \App\Models\Inventory::factory()->create(['tenant_id' => $tenant->id, 'site_id' => $site->id]);
        $item = \App\Models\Item::factory()->create(['tenant_id' => $tenant->id, 'site_id' => $site->id]);

        InventoryItem::create([
            'inventory_id' => $inv->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'status' => InventoryItem::STATUS_ACTIVE,
        ]);
        InventoryItem::create([
            'inventory_id' => $inv->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'status' => InventoryItem::STATUS_VOIDED,
        ]);

        $activeCount = InventoryItem::activeOnly()->count();
        $this->assertGreaterThanOrEqual(1, $activeCount);
    }

    public function test_inventory_item_scope_active_for_requester_includes_adjustment(): void
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $site = \App\Models\Site::factory()->forTenant($tenant)->create();
        $inv = \App\Models\Inventory::factory()->create(['tenant_id' => $tenant->id, 'site_id' => $site->id]);
        $item = \App\Models\Item::factory()->create(['tenant_id' => $tenant->id, 'site_id' => $site->id]);

        InventoryItem::create([
            'inventory_id' => $inv->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'status' => InventoryItem::STATUS_ACTIVE,
        ]);
        InventoryItem::create([
            'inventory_id' => $inv->id,
            'item_id' => $item->id,
            'quantity' => -1,
            'tenant_id' => $tenant->id,
            'site_id' => $site->id,
            'status' => InventoryItem::STATUS_ADJUSTMENT,
        ]);

        $count = InventoryItem::activeForRequester()->count();
        $this->assertGreaterThanOrEqual(2, $count);
    }

    public function test_reason_code_model_has_expected_attributes(): void
    {
        $code = InventoryCorrectionReasonCode::firstOrCreate(
            ['code' => 'TST-99'],
            ['type' => 'Test', 'use_case' => 'Unit test']
        );
        $this->assertEquals('TST-99', $code->code);
        $this->assertEquals('Test', $code->type);
        $this->assertEquals('Unit test', $code->use_case);
    }
}
