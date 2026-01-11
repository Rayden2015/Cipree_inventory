<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add tenant_id to all tables that have site_id to enable multi-tenancy
     */
    public function up(): void
    {
        // Tables to add tenant_id to (all tables that contain tenant-specific business data)
        $tables = [
            'orders',
            'porders',
            'sorders',
            'inventory_items',
            'inventories',
            'items',
            'departments',
            'sections',
            'locations',
            'suppliers',
            'endusers',
            'end_users_categories',
            'categories',
            'companies',
            'purchases',
            'order_parts',
            'porder_parts',
            'sorder_parts',
            'stock_purchase_requests',
            'spr_porders',
            'spr_porder_items',
            'inventory_item_details',
            'taxes',
            'levies',
            'notifications',
            'logins',
            'parts',
            'employees',
            'uoms', // Units of Measure - tenant-specific for better isolation
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // For tables without site_id, add tenant_id after id
                    // For tables with site_id, add tenant_id after site_id
                    $hasSiteId = Schema::hasColumn($tableName, 'site_id');
                    $afterColumn = $hasSiteId ? 'site_id' : 'id';
                    
                    $table->unsignedBigInteger('tenant_id')->nullable()->after($afterColumn);
                    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                    $table->index('tenant_id'); // Add index for better query performance
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'orders',
            'porders',
            'sorders',
            'inventory_items',
            'inventories',
            'items',
            'departments',
            'sections',
            'locations',
            'suppliers',
            'endusers',
            'end_users_categories',
            'categories',
            'companies',
            'purchases',
            'order_parts',
            'porder_parts',
            'sorder_parts',
            'stock_purchase_requests',
            'spr_porders',
            'spr_porder_items',
            'inventory_item_details',
            'taxes',
            'levies',
            'notifications',
            'logins',
            'parts',
            'employees',
            'uoms', // Units of Measure - tenant-specific
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Drop foreign key by column name (Laravel will find the constraint name)
                    $table->dropForeign(['tenant_id']);
                    // Drop index by column name
                    try {
                        $table->dropIndex([$tableName . '_tenant_id_index']);
                    } catch (\Exception $e) {
                        // Index might have a different name, try alternative
                        $table->dropIndex(['tenant_id']);
                    }
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
