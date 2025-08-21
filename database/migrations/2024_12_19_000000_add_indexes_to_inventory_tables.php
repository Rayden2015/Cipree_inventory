<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to inventory_items table
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->index('site_id');                    // For WHERE clause filtering
            $table->index('inventory_id');               // For JOIN with inventories table
            $table->index('item_id');                    // For JOIN with items table
            $table->index(['site_id', 'item_id']);       // Composite index for common queries
        });

        // Add indexes to items table
        Schema::table('items', function (Blueprint $table) {
            $table->index('site_id');                    // For site-based filtering
            $table->index('stock_quantity');             // For stock quantity filtering
            $table->index(['site_id', 'stock_quantity']); // Composite index for the WHERE clause
        });

        // Add indexes to inventories table
        Schema::table('inventories', function (Blueprint $table) {
            $table->index('id');                         // Primary key should already be indexed
            $table->index('trans_type');                 // For GROUP BY
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from inventory_items table
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex(['site_id']);
            $table->dropIndex(['inventory_id']);
            $table->dropIndex(['item_id']);
            $table->dropIndex(['site_id', 'item_id']);
        });

        // Remove indexes from items table
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['site_id']);
            $table->dropIndex(['stock_quantity']);
            $table->dropIndex(['site_id', 'stock_quantity']);
        });

        // Remove indexes from inventories table
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['trans_type']);
        });
    }
};
