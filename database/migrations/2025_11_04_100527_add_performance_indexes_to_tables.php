<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds indexes to improve query performance and resolve N+1 query issues.
     */
    public function up(): void
    {
        // Orders table indexes - for home dashboard and user queries
        Schema::table('orders', function (Blueprint $table) {
            // Index for user_id + status combinations (for user dashboard queries)
            $table->index(['user_id', 'status'], 'idx_orders_user_status');
            
            // Separate indexes for site queries (composite with 3 varchar columns too long)
            $table->index('site_id', 'idx_orders_site_id');
            $table->index('status', 'idx_orders_status');
            $table->index('approval_status', 'idx_orders_approval_status');
            
            // Composite index with site_id and status only (2 columns)
            $table->index(['site_id', 'status'], 'idx_orders_site_status');
        });

        // Sorders table indexes - for store requests and supply history
        Schema::table('sorders', function (Blueprint $table) {
            // Single column indexes for better compatibility
            $table->index('site_id', 'idx_sorders_site_id');
            $table->index('status', 'idx_sorders_status');
            $table->index('approval_status', 'idx_sorders_approval_status');
            
            // Index for requested_by user queries
            $table->index('requested_by', 'idx_sorders_requested_by');
            
            // Index for delivered_on for supply history sorting
            $table->index('delivered_on', 'idx_sorders_delivered_on');
            
            // Index for enduser_id to prevent N+1 queries
            $table->index('enduser_id', 'idx_sorders_enduser_id');
            
            // Index for user_id for department joins
            $table->index('user_id', 'idx_sorders_user_id');
        });

        // Sorder_parts table indexes - for supply history searches
        Schema::table('sorder_parts', function (Blueprint $table) {
            // Index for sorder_id foreign key lookups
            $table->index('sorder_id', 'idx_sorder_parts_sorder_id');
            
            // Index for site_id filtering
            $table->index('site_id', 'idx_sorder_parts_site_id');
            
            // Index for item_id lookups
            $table->index('item_id', 'idx_sorder_parts_item_id');
            
            // Index for inventory_id for location lookups
            $table->index('inventory_id', 'idx_sorder_parts_inventory_id');
            
            // Composite index for complex supply history queries
            $table->index(['site_id', 'sorder_id'], 'idx_sorder_parts_site_sorder');
        });

        // Endusers table indexes - for lookups and searches
        Schema::table('endusers', function (Blueprint $table) {
            // Index for asset_staff_id searches
            $table->index('asset_staff_id', 'idx_endusers_asset_staff_id');
            
            // Index for site_id filtering
            $table->index('site_id', 'idx_endusers_site_id');
            
            // Index for department_id
            $table->index('department_id', 'idx_endusers_department_id');
            
            // Index for section_id
            $table->index('section_id', 'idx_endusers_section_id');
            
            // Index for type (for category filtering)
            $table->index('type', 'idx_endusers_type');
            
            // Index for status
            $table->index('status', 'idx_endusers_status');
        });

        // Inventories table indexes - for history and search queries
        Schema::table('inventories', function (Blueprint $table) {
            // Index for enduser_id to prevent N+1 queries
            $table->index('enduser_id', 'idx_inventories_enduser_id');
            
            // Index for site_id filtering
            $table->index('site_id', 'idx_inventories_site_id');
            
            // Index for po_number searches
            $table->index('po_number', 'idx_inventories_po_number');
            
            // Index for grn_number searches
            $table->index('grn_number', 'idx_inventories_grn_number');
            
            // Index for created_at for date range queries
            $table->index('created_at', 'idx_inventories_created_at');
        });

        // Inventory_items table indexes - for stock and location queries
        Schema::table('inventory_items', function (Blueprint $table) {
            // Index for inventory_id foreign key
            $table->index('inventory_id', 'idx_inventory_items_inventory_id');
            
            // Index for item_id
            $table->index('item_id', 'idx_inventory_items_item_id');
            
            // Index for location_id to prevent N+1 queries
            $table->index('location_id', 'idx_inventory_items_location_id');
            
            // Index for site_id filtering
            $table->index('site_id', 'idx_inventory_items_site_id');
            
            // Composite index for stock queries
            $table->index(['site_id', 'item_id'], 'idx_inventory_items_site_item');
            
            // Index for quantity checks
            $table->index('quantity', 'idx_inventory_items_quantity');
        });

        // Inventory_item_details table indexes - for history queries
        Schema::table('inventory_item_details', function (Blueprint $table) {
            // Index for inventory_id
            $table->index('inventory_id', 'idx_inventory_details_inventory_id');
            
            // Index for item_id
            $table->index('item_id', 'idx_inventory_details_item_id');
            
            // Index for site_id filtering
            $table->index('site_id', 'idx_inventory_details_site_id');
            
            // Index for created_at for date range queries
            $table->index('created_at', 'idx_inventory_details_created_at');
        });

        // Items table indexes - for search queries
        Schema::table('items', function (Blueprint $table) {
            // Index for item_description searches
            $table->index('item_description', 'idx_items_description');
            
            // Index for item_part_number searches
            $table->index('item_part_number', 'idx_items_part_number');
            
            // Index for item_stock_code searches
            $table->index('item_stock_code', 'idx_items_stock_code');
            
            // Index for stock_quantity for availability checks
            $table->index('stock_quantity', 'idx_items_stock_quantity');
        });

        // Users table indexes - for user queries
        Schema::table('users', function (Blueprint $table) {
            // Index for site_id filtering
            $table->index('site_id', 'idx_users_site_id');
            
            // Index for department_id
            $table->index('department_id', 'idx_users_department_id');
            
            // Index for status
            $table->index('status', 'idx_users_status');
            
            // Composite index for site + status queries
            $table->index(['site_id', 'status'], 'idx_users_site_status');
        });

        // Porder_parts table indexes - for purchase order queries
        Schema::table('porder_parts', function (Blueprint $table) {
            // Index for order_id
            $table->index('order_id', 'idx_porder_parts_order_id');
            
            // Index for site_id filtering
            $table->index('site_id', 'idx_porder_parts_site_id');
        });

        // Porders table indexes - for purchase order queries  
        Schema::table('porders', function (Blueprint $table) {
            // Single column indexes (avoid composite with multiple varchar columns)
            $table->index('site_id', 'idx_porders_site_id');
            $table->index('status', 'idx_porders_status');
            $table->index('approval_status', 'idx_porders_approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all indexes in reverse order
        Schema::table('porders', function (Blueprint $table) {
            $table->dropIndex('idx_porders_site_id');
            $table->dropIndex('idx_porders_status');
            $table->dropIndex('idx_porders_approval_status');
        });

        Schema::table('porder_parts', function (Blueprint $table) {
            $table->dropIndex('idx_porder_parts_order_id');
            $table->dropIndex('idx_porder_parts_site_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_site_id');
            $table->dropIndex('idx_users_department_id');
            $table->dropIndex('idx_users_status');
            $table->dropIndex('idx_users_site_status');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('idx_items_description');
            $table->dropIndex('idx_items_part_number');
            $table->dropIndex('idx_items_stock_code');
            $table->dropIndex('idx_items_stock_quantity');
        });

        Schema::table('inventory_item_details', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_details_inventory_id');
            $table->dropIndex('idx_inventory_details_item_id');
            $table->dropIndex('idx_inventory_details_site_id');
            $table->dropIndex('idx_inventory_details_created_at');
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_items_inventory_id');
            $table->dropIndex('idx_inventory_items_item_id');
            $table->dropIndex('idx_inventory_items_location_id');
            $table->dropIndex('idx_inventory_items_site_id');
            $table->dropIndex('idx_inventory_items_site_item');
            $table->dropIndex('idx_inventory_items_quantity');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex('idx_inventories_enduser_id');
            $table->dropIndex('idx_inventories_site_id');
            $table->dropIndex('idx_inventories_po_number');
            $table->dropIndex('idx_inventories_grn_number');
            $table->dropIndex('idx_inventories_created_at');
        });

        Schema::table('endusers', function (Blueprint $table) {
            $table->dropIndex('idx_endusers_asset_staff_id');
            $table->dropIndex('idx_endusers_site_id');
            $table->dropIndex('idx_endusers_department_id');
            $table->dropIndex('idx_endusers_section_id');
            $table->dropIndex('idx_endusers_type');
            $table->dropIndex('idx_endusers_status');
        });

        Schema::table('sorder_parts', function (Blueprint $table) {
            $table->dropIndex('idx_sorder_parts_sorder_id');
            $table->dropIndex('idx_sorder_parts_site_id');
            $table->dropIndex('idx_sorder_parts_item_id');
            $table->dropIndex('idx_sorder_parts_inventory_id');
            $table->dropIndex('idx_sorder_parts_site_sorder');
        });

        Schema::table('sorders', function (Blueprint $table) {
            $table->dropIndex('idx_sorders_site_id');
            $table->dropIndex('idx_sorders_status');
            $table->dropIndex('idx_sorders_approval_status');
            $table->dropIndex('idx_sorders_requested_by');
            $table->dropIndex('idx_sorders_delivered_on');
            $table->dropIndex('idx_sorders_enduser_id');
            $table->dropIndex('idx_sorders_user_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user_status');
            $table->dropIndex('idx_orders_site_id');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_approval_status');
            $table->dropIndex('idx_orders_site_status');
        });
    }
};
