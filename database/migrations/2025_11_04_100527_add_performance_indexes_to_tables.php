<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        $tableName = $connection->getTablePrefix() . $table;
        
        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$databaseName, $tableName, $indexName]
        );
        
        return $result[0]->count > 0;
    }

    /**
     * Run the migrations.
     * 
     * This migration adds indexes to improve query performance and resolve N+1 query issues.
     */
    public function up(): void
    {
        // Orders table indexes - for home dashboard and user queries
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                // Index for user_id + status combinations (for user dashboard queries)
                if (!$this->indexExists('orders', 'idx_orders_user_status')) {
                    $table->index(['user_id', 'status'], 'idx_orders_user_status');
                }
                
                // Separate indexes for site queries (composite with 3 varchar columns too long)
                if (!$this->indexExists('orders', 'idx_orders_site_id')) {
                    $table->index('site_id', 'idx_orders_site_id');
                }
                if (!$this->indexExists('orders', 'idx_orders_status')) {
                    $table->index('status', 'idx_orders_status');
                }
                if (!$this->indexExists('orders', 'idx_orders_approval_status')) {
                    $table->index('approval_status', 'idx_orders_approval_status');
                }
                
                // Composite index with site_id and status only (2 columns)
                if (!$this->indexExists('orders', 'idx_orders_site_status')) {
                    $table->index(['site_id', 'status'], 'idx_orders_site_status');
                }
            });
        }

        // Sorders table indexes - for store requests and supply history
        if (Schema::hasTable('sorders')) {
            Schema::table('sorders', function (Blueprint $table) {
                // Single column indexes for better compatibility
                if (!$this->indexExists('sorders', 'idx_sorders_site_id')) {
                    $table->index('site_id', 'idx_sorders_site_id');
                }
                if (!$this->indexExists('sorders', 'idx_sorders_status')) {
                    $table->index('status', 'idx_sorders_status');
                }
                if (!$this->indexExists('sorders', 'idx_sorders_approval_status')) {
                    $table->index('approval_status', 'idx_sorders_approval_status');
                }
                
                // Index for requested_by user queries
                if (!$this->indexExists('sorders', 'idx_sorders_requested_by')) {
                    $table->index('requested_by', 'idx_sorders_requested_by');
                }
                
                // Index for delivered_on for supply history sorting
                if (!$this->indexExists('sorders', 'idx_sorders_delivered_on')) {
                    $table->index('delivered_on', 'idx_sorders_delivered_on');
                }
                
                // Index for enduser_id to prevent N+1 queries
                if (!$this->indexExists('sorders', 'idx_sorders_enduser_id')) {
                    $table->index('enduser_id', 'idx_sorders_enduser_id');
                }
                
                // Index for user_id for department joins
                if (!$this->indexExists('sorders', 'idx_sorders_user_id')) {
                    $table->index('user_id', 'idx_sorders_user_id');
                }
            });
        }

        // Sorder_parts table indexes - for supply history searches
        if (Schema::hasTable('sorder_parts')) {
            Schema::table('sorder_parts', function (Blueprint $table) {
                // Index for sorder_id foreign key lookups
                if (!$this->indexExists('sorder_parts', 'idx_sorder_parts_sorder_id')) {
                    $table->index('sorder_id', 'idx_sorder_parts_sorder_id');
                }
                
                // Index for site_id filtering
                if (!$this->indexExists('sorder_parts', 'idx_sorder_parts_site_id')) {
                    $table->index('site_id', 'idx_sorder_parts_site_id');
                }
                
                // Index for item_id lookups
                if (!$this->indexExists('sorder_parts', 'idx_sorder_parts_item_id')) {
                    $table->index('item_id', 'idx_sorder_parts_item_id');
                }
                
                // Index for inventory_id for location lookups
                if (!$this->indexExists('sorder_parts', 'idx_sorder_parts_inventory_id')) {
                    $table->index('inventory_id', 'idx_sorder_parts_inventory_id');
                }
                
                // Composite index for complex supply history queries
                if (!$this->indexExists('sorder_parts', 'idx_sorder_parts_site_sorder')) {
                    $table->index(['site_id', 'sorder_id'], 'idx_sorder_parts_site_sorder');
                }
            });
        }

        // Endusers table indexes - for lookups and searches
        if (Schema::hasTable('endusers')) {
            Schema::table('endusers', function (Blueprint $table) {
                // Index for asset_staff_id searches (only if column exists)
                if (Schema::hasColumn('endusers', 'asset_staff_id') && !$this->indexExists('endusers', 'idx_endusers_asset_staff_id')) {
                    $table->index('asset_staff_id', 'idx_endusers_asset_staff_id');
                }
                
                // Index for site_id filtering
                if (Schema::hasColumn('endusers', 'site_id') && !$this->indexExists('endusers', 'idx_endusers_site_id')) {
                    $table->index('site_id', 'idx_endusers_site_id');
                }
                
                // Index for department_id
                if (Schema::hasColumn('endusers', 'department_id') && !$this->indexExists('endusers', 'idx_endusers_department_id')) {
                    $table->index('department_id', 'idx_endusers_department_id');
                }
                
                // Index for section_id
                if (Schema::hasColumn('endusers', 'section_id') && !$this->indexExists('endusers', 'idx_endusers_section_id')) {
                    $table->index('section_id', 'idx_endusers_section_id');
                }
                
                // Index for type (for category filtering)
                if (Schema::hasColumn('endusers', 'type') && !$this->indexExists('endusers', 'idx_endusers_type')) {
                    $table->index('type', 'idx_endusers_type');
                }
                
                // Index for status
                if (Schema::hasColumn('endusers', 'status') && !$this->indexExists('endusers', 'idx_endusers_status')) {
                    $table->index('status', 'idx_endusers_status');
                }
            });
        }

        // Inventories table indexes - for history and search queries
        if (Schema::hasTable('inventories')) {
            Schema::table('inventories', function (Blueprint $table) {
                // Index for enduser_id to prevent N+1 queries
                if (!$this->indexExists('inventories', 'idx_inventories_enduser_id')) {
                    $table->index('enduser_id', 'idx_inventories_enduser_id');
                }
                
                // Index for site_id filtering
                if (!$this->indexExists('inventories', 'idx_inventories_site_id')) {
                    $table->index('site_id', 'idx_inventories_site_id');
                }
                
                // Index for po_number searches
                if (!$this->indexExists('inventories', 'idx_inventories_po_number')) {
                    $table->index('po_number', 'idx_inventories_po_number');
                }
                
                // Index for grn_number searches
                if (!$this->indexExists('inventories', 'idx_inventories_grn_number')) {
                    $table->index('grn_number', 'idx_inventories_grn_number');
                }
                
                // Index for created_at for date range queries
                if (!$this->indexExists('inventories', 'idx_inventories_created_at')) {
                    $table->index('created_at', 'idx_inventories_created_at');
                }
            });
        }

        // Inventory_items table indexes - for stock and location queries
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                // Index for inventory_id foreign key
                if (!$this->indexExists('inventory_items', 'idx_inventory_items_inventory_id')) {
                    $table->index('inventory_id', 'idx_inventory_items_inventory_id');
                }
                
                // Index for item_id
                if (!$this->indexExists('inventory_items', 'idx_inventory_items_item_id')) {
                    $table->index('item_id', 'idx_inventory_items_item_id');
                }
                
                // Index for location_id to prevent N+1 queries
                if (!$this->indexExists('inventory_items', 'idx_inventory_items_location_id')) {
                    $table->index('location_id', 'idx_inventory_items_location_id');
                }
                
                // Index for site_id filtering
                if (!$this->indexExists('inventory_items', 'idx_inventory_items_site_id')) {
                    $table->index('site_id', 'idx_inventory_items_site_id');
                }
                
                // Composite index for stock queries
                if (!$this->indexExists('inventory_items', 'idx_inventory_items_site_item')) {
                    $table->index(['site_id', 'item_id'], 'idx_inventory_items_site_item');
                }
                
                // Index for quantity checks
                if (!$this->indexExists('inventory_items', 'idx_inventory_items_quantity')) {
                    $table->index('quantity', 'idx_inventory_items_quantity');
                }
            });
        }

        // Inventory_item_details table indexes - for history queries
        if (Schema::hasTable('inventory_item_details')) {
            Schema::table('inventory_item_details', function (Blueprint $table) {
                // Index for inventory_id
                if (!$this->indexExists('inventory_item_details', 'idx_inventory_details_inventory_id')) {
                    $table->index('inventory_id', 'idx_inventory_details_inventory_id');
                }
                
                // Index for item_id
                if (!$this->indexExists('inventory_item_details', 'idx_inventory_details_item_id')) {
                    $table->index('item_id', 'idx_inventory_details_item_id');
                }
                
                // Index for site_id filtering
                if (!$this->indexExists('inventory_item_details', 'idx_inventory_details_site_id')) {
                    $table->index('site_id', 'idx_inventory_details_site_id');
                }
                
                // Index for created_at for date range queries
                if (!$this->indexExists('inventory_item_details', 'idx_inventory_details_created_at')) {
                    $table->index('created_at', 'idx_inventory_details_created_at');
                }
            });
        }

        // Items table indexes - for search queries
        if (Schema::hasTable('items')) {
            Schema::table('items', function (Blueprint $table) {
                // Index for item_description searches
                if (!$this->indexExists('items', 'idx_items_description')) {
                    $table->index('item_description', 'idx_items_description');
                }
                
                // Index for item_part_number searches
                if (!$this->indexExists('items', 'idx_items_part_number')) {
                    $table->index('item_part_number', 'idx_items_part_number');
                }
                
                // Index for item_stock_code searches
                if (!$this->indexExists('items', 'idx_items_stock_code')) {
                    $table->index('item_stock_code', 'idx_items_stock_code');
                }
                
                // Index for stock_quantity for availability checks
                if (!$this->indexExists('items', 'idx_items_stock_quantity')) {
                    $table->index('stock_quantity', 'idx_items_stock_quantity');
                }
            });
        }

        // Users table indexes - for user queries
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Index for site_id filtering
                if (!$this->indexExists('users', 'idx_users_site_id')) {
                    $table->index('site_id', 'idx_users_site_id');
                }
                
                // Index for department_id
                if (!$this->indexExists('users', 'idx_users_department_id')) {
                    $table->index('department_id', 'idx_users_department_id');
                }
                
                // Index for status
                if (!$this->indexExists('users', 'idx_users_status')) {
                    $table->index('status', 'idx_users_status');
                }
                
                // Composite index for site + status queries
                if (!$this->indexExists('users', 'idx_users_site_status')) {
                    $table->index(['site_id', 'status'], 'idx_users_site_status');
                }
            });
        }

        // Porder_parts table indexes - for purchase order queries
        if (Schema::hasTable('porder_parts')) {
            Schema::table('porder_parts', function (Blueprint $table) {
                // Index for order_id
                if (!$this->indexExists('porder_parts', 'idx_porder_parts_order_id')) {
                    $table->index('order_id', 'idx_porder_parts_order_id');
                }
                
                // Index for site_id filtering
                if (!$this->indexExists('porder_parts', 'idx_porder_parts_site_id')) {
                    $table->index('site_id', 'idx_porder_parts_site_id');
                }
            });
        }

        // Porders table indexes - for purchase order queries  
        if (Schema::hasTable('porders')) {
            Schema::table('porders', function (Blueprint $table) {
                // Single column indexes (avoid composite with multiple varchar columns)
                if (!$this->indexExists('porders', 'idx_porders_site_id')) {
                    $table->index('site_id', 'idx_porders_site_id');
                }
                if (!$this->indexExists('porders', 'idx_porders_status')) {
                    $table->index('status', 'idx_porders_status');
                }
                if (!$this->indexExists('porders', 'idx_porders_approval_status')) {
                    $table->index('approval_status', 'idx_porders_approval_status');
                }
            });
        }
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

        if (Schema::hasTable('endusers')) {
            Schema::table('endusers', function (Blueprint $table) {
                // Only drop indexes if they exist
                try {
                    $table->dropIndex('idx_endusers_asset_staff_id');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('idx_endusers_site_id');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('idx_endusers_department_id');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('idx_endusers_section_id');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('idx_endusers_type');
                } catch (\Exception $e) {}
                try {
                    $table->dropIndex('idx_endusers_status');
                } catch (\Exception $e) {}
            });
        }

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
