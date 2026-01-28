<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\{User, Site, Department, Enduser, EndUsersCategory, Supplier, Item, Inventory, InventoryItem, Sorder, SorderPart, Tenant};
use Carbon\Carbon;

class StoreRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create a basic store request cart context.
     */
    protected function createStoreRequestContext(string $departmentName = 'Operations'): array
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Requester', 'guard_name' => 'web']);

        $tenant = Tenant::factory()->create([
            'name' => 'Test Tenant',
            'status' => 'Active',
        ]);

        $site = Site::create([
            'name' => 'Main Site',
            'site_code' => 'MS',
            'tenant_id' => $tenant->id,
        ]);

        $department = Department::create([
            'name' => $departmentName,
            'description' => $departmentName . ' Department',
            'site_id' => $site->id,
            'tenant_id' => $tenant->id,
        ]);

        $requester = User::factory()->create([
            'site_id' => $site->id,
            'tenant_id' => $tenant->id,
            'department_id' => $department->id,
            'status' => 'Active',
        ]);
        $requester->assignRole('Requester');

        $enduserCategory = EndUsersCategory::forceCreate([
            'name' => 'General Staff',
            'site_id' => $site->id,
        ]);

        $enduser = Enduser::forceCreate([
            'name' => 'John Doe',
            'type' => 'Staff',
            'department' => $departmentName,
            'site_id' => $site->id,
            'enduser_category_id' => $enduserCategory->id,
        ]);

        $supplier = Supplier::create([
            'name' => 'Acme Supplies',
            'site_id' => $site->id,
        ]);

        $inventory = Inventory::create([
            'supplier_id' => $supplier->id,
            'site_id' => $site->id,
            'po_number' => 'PO-1001',
            'grn_number' => 'GRN-1001',
            'date' => Carbon::now(),
            'billing_currency' => 'GHS',
        ]);

        $item = Item::create([
            'item_description' => 'Dell Laptop',
            'item_uom' => 'EA',
            'item_stock_code' => 'DL-100',
            'item_part_number' => 'DL100',
            'site_id' => $site->id,
            'amount' => 500,
            'stock_quantity' => 10,
        ]);

        $inventoryItem = InventoryItem::create([
            'inventory_id' => $inventory->id,
            'item_id' => $item->id,
            'quantity' => 10,
            'unit_cost_exc_vat_gh' => 50,
            'site_id' => $site->id,
        ]);

        $cart = [
            $inventoryItem->id => [
                'id' => $inventoryItem->id,
                'item_id' => $item->id,
                'item_description' => $item->item_description,
                'item_uom' => $item->item_uom,
                'item_part_number' => $item->item_part_number,
                'item_stock_code' => $item->item_stock_code,
                'unit_cost_exc_vat_gh' => $inventoryItem->unit_cost_exc_vat_gh,
                'quantity' => 2,
                'price' => $inventoryItem->unit_cost_exc_vat_gh,
                'image' => null,
            ],
        ];

        return compact(
            'tenant',
            'site',
            'department',
            'requester',
            'enduser',
            'supplier',
            'inventory',
            'item',
            'inventoryItem',
            'cart'
        );
    }

    public function test_engineering_department_requires_work_order_number(): void
    {
        $context = $this->createStoreRequestContext('Engineering');

        $this->actingAs($context['requester']);

        $payload = [
            'request_number' => 'SR-ENG-1001',
            'request_date' => Carbon::now()->toDateString(),
            'supplier_id' => $context['supplier']->id,
            'type_of_purchase' => 'Internal',
            'enduser_id' => $context['enduser']->id,
            'currency' => 'GHS',
            // Intentionally omit work_order_number
        ];

        $response = $this->withSession(['cart' => $context['cart']])
            ->from(route('stores.request_search'))
            ->post(route('sorders.store'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['work_order_number']);
        $this->assertEquals(0, Sorder::count());
    }

    public function test_non_engineering_department_does_not_require_work_order_number(): void
    {
        $context = $this->createStoreRequestContext('Operations');

        $this->actingAs($context['requester']);

        $payload = [
            'request_number' => 'SR-OPS-1001',
            'request_date' => Carbon::now()->toDateString(),
            'supplier_id' => $context['supplier']->id,
            'type_of_purchase' => 'Internal',
            'enduser_id' => $context['enduser']->id,
            'currency' => 'GHS',
            // No work_order_number on purpose
        ];

        $response = $this->withSession(['cart' => $context['cart']])
            ->post(route('sorders.store'), $payload);

        $response->assertRedirect(route('stores.request_search'));
        $response->assertSessionHas('success');

        $sorder = Sorder::first();
        $this->assertNotNull($sorder);
        $this->assertNull($sorder->work_order_number);
    }

    public function test_requester_request_through_store_officer_processing_flow(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['Requester', 'Department Authoriser', 'Authoriser', 'Store Officer'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }

        $tenant = Tenant::factory()->create([
            'name' => 'Test Tenant',
            'status' => 'Active',
        ]);

        $site = Site::create([
            'name' => 'Main Site',
            'site_code' => 'MS',
            'tenant_id' => $tenant->id,
        ]);

        $department = Department::create([
            'name' => 'Operations',
            'description' => 'Ops Department',
            'site_id' => $site->id,
            'tenant_id' => $tenant->id,
        ]);

        $requester = User::factory()->create([
            'site_id' => $site->id,
            'tenant_id' => $tenant->id,
            'department_id' => $department->id,
            'status' => 'Active',
        ]);
        $requester->assignRole('Requester');

        $departmentAuthoriser = User::factory()->create([
            'site_id' => $site->id,
            'department_id' => $department->id,
            'status' => 'Active',
        ]);
        $departmentAuthoriser->assignRole('Department Authoriser');

        $approver = User::factory()->create([
            'site_id' => $site->id,
            'status' => 'Active',
        ]);
        $approver->assignRole('Authoriser');

        $storeOfficer = User::factory()->create([
            'site_id' => $site->id,
            'status' => 'Active',
        ]);
        $storeOfficer->assignRole('Store Officer');

        $enduserCategory = EndUsersCategory::forceCreate([
            'name' => 'General Staff',
            'site_id' => $site->id,
        ]);

        $enduser = Enduser::forceCreate([
            'name' => 'John Doe',
            'type' => 'Staff',
            'department' => 'Operations',
            'site_id' => $site->id,
            'enduser_category_id' => $enduserCategory->id,
        ]);

        $supplier = Supplier::create([
            'name' => 'Acme Supplies',
            'site_id' => $site->id,
        ]);

        $inventory = Inventory::create([
            'supplier_id' => $supplier->id,
            'site_id' => $site->id,
            'po_number' => 'PO-1001',
            'grn_number' => 'GRN-1001',
            'date' => Carbon::now(),
            'billing_currency' => 'GHS',
        ]);

        $item = Item::create([
            'item_description' => 'Dell Laptop',
            'item_uom' => 'EA',
            'item_stock_code' => 'DL-100',
            'item_part_number' => 'DL100',
            'site_id' => $site->id,
            'amount' => 500,
            'stock_quantity' => 10,
        ]);

        $inventoryItem = InventoryItem::create([
            'inventory_id' => $inventory->id,
            'item_id' => $item->id,
            'quantity' => 10,
            'unit_cost_exc_vat_gh' => 50,
            'site_id' => $site->id,
        ]);

        $cart = [
            $inventoryItem->id => [
                'id' => $inventoryItem->id,
                'item_id' => $item->id,
                'item_description' => $item->item_description,
                'item_uom' => $item->item_uom,
                'item_part_number' => $item->item_part_number,
                'item_stock_code' => $item->item_stock_code,
                'unit_cost_exc_vat_gh' => $inventoryItem->unit_cost_exc_vat_gh,
                'quantity' => 2,
                'price' => $inventoryItem->unit_cost_exc_vat_gh,
                'image' => null,
            ],
        ];

        $requestPayload = [
            'request_number' => 'SR-1001',
            'request_date' => Carbon::now()->toDateString(),
            'supplier_id' => $supplier->id,
            'type_of_purchase' => 'Internal',
            'enduser_id' => $enduser->id,
            'currency' => 'GHS',
            'work_order_number' => 5001,
        ];

        $this->actingAs($requester);
        $response = $this->withSession(['cart' => $cart])->post(route('sorders.store'), $requestPayload);
        $response->assertRedirect(route('stores.request_search'));
        $response->assertSessionHas('success');

        $sorder = Sorder::first();
        $this->assertNotNull($sorder);
        $this->assertEquals('Requested', $sorder->status);
        $this->assertEquals($requester->id, $sorder->user_id);
        $this->assertEquals($site->id, $sorder->site_id);
        $this->assertEquals(5001, $sorder->work_order_number);

        $sorderPart = SorderPart::first();
        $this->assertNotNull($sorderPart);
        $this->assertEquals(2, $sorderPart->quantity);
        $this->assertEquals($inventoryItem->id, $sorderPart->inventory_id);

        $sorderPart->update([
            'qty_supplied' => 3, // intentionally higher than requested to validate oversupply protection
            'unit_price' => $inventoryItem->unit_cost_exc_vat_gh,
        ]);

        $storeOfficerPayload = [
            'tax' => 0,
            'tax2' => 0,
            'tax3' => 0,
            'supplier_id' => $supplier->id,
            'type_of_purchase' => 'Internal',
            'enduser_id' => $enduser->id,
            'invoice_number' => 'INV-1001',
            'supplied_to' => 'Operations',
        ];

        // Store officer cannot process before approvals are complete
        $this->actingAs($storeOfficer)
            ->from(route('stores.store_officer_edit', $sorder->id))
            ->put(route('stores.store_officer_update', $sorder->id), $storeOfficerPayload)
            ->assertStatus(302)
            ->assertSessionHas('error', 'This request must be fully approved before it can be processed by Stores.');

        $sorder->refresh();

        // Supply chain approver cannot approve before the department
        $this->actingAs($approver)
            ->from(route('sorders.store_lists'))
            ->get(route('stores.approved_status', $sorder->id))
            ->assertStatus(302)
            ->assertSessionHas('error', 'Department approval must be completed before Supply Chain can approve.');

        $sorder->refresh();
        $this->assertNull($sorder->approved_by);
        $this->assertNull($sorder->approved_on);

        // Supply chain approver cannot deny before the department
        $this->actingAs($approver)
            ->from(route('sorders.store_lists'))
            ->get(route('stores.denied_status', $sorder->id))
            ->assertStatus(302)
            ->assertSessionHas('error', 'Department approval must be completed before Supply Chain can deny.');

        $sorder->refresh();
        $this->assertNotEquals('Denied', $sorder->approval_status);
        $this->assertNull($sorder->approved_by);
        $this->assertNull($sorder->approved_on);

        // Department Authoriser approval
        $this->actingAs($departmentAuthoriser)
            ->from(route('sorders.store_lists'))
            ->get(route('stores.depart_auth_approved_status', $sorder->id))
            ->assertStatus(302);

        $sorder->refresh();
        $this->assertEquals('Approved', $sorder->depart_auth_approval_status);
        $this->assertEquals($departmentAuthoriser->id, $sorder->depart_auth_approved_by);
        $this->assertNotNull($sorder->depart_auth_approved_on);

        // Final Authoriser approval
        $this->actingAs($approver)
            ->from(route('sorders.store_lists'))
            ->get(route('stores.approved_status', $sorder->id))
            ->assertStatus(302);

        $sorder->refresh();
        $this->assertEquals('Approved', $sorder->approval_status);
        $this->assertEquals($approver->id, $sorder->approved_by);
        $this->assertNotNull($sorder->approved_on);

        // Oversupply (3 > 2) should be rejected even after approvals
        $response = $this->actingAs($storeOfficer)
            ->from(route('stores.store_officer_edit', $sorder->id))
            ->put(route('stores.store_officer_update', $sorder->id), $storeOfficerPayload);

        $response->assertStatus(302)
            ->assertSessionHas('error', function ($message) {
                return str_contains($message, 'Quantity supplied (3) cannot exceed the requested quantity (2)');
            });

        // Correct the quantity and process successfully
        $sorderPart->refresh();
        $sorderPart->update(['qty_supplied' => 2]);

        $response = $this->actingAs($storeOfficer)
            ->from(route('stores.store_officer_edit', $sorder->id))
            ->put(route('stores.store_officer_update', $sorder->id), $storeOfficerPayload);
        
        $response->assertStatus(302);

        $sorder->refresh();
        $inventoryItem->refresh();

        $this->assertEquals('Supplied', $sorder->status);
        $this->assertEquals($storeOfficer->id, $sorder->delivered_by);
        $this->assertNotNull($sorder->delivered_on);
        $this->assertEquals('SR-1001', $sorder->delivery_reference_number);
    }
}
