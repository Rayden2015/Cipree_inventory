<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_items', 'status')) {
                $table->string('status', 20)->default('Active')->after('tenant_id');
            }
            if (!Schema::hasColumn('inventory_items', 'source_inventory_item_id')) {
                $table->unsignedBigInteger('source_inventory_item_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('inventory_items', 'current_net_balance')) {
                $table->integer('current_net_balance')->nullable()->after('source_inventory_item_id');
            }
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_items', 'source_inventory_item_id')) {
                $table->foreign('source_inventory_item_id')
                    ->references('id')->on('inventory_items')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_items', 'source_inventory_item_id')) {
                $table->dropForeign(['source_inventory_item_id']);
            }
            foreach (['status', 'source_inventory_item_id', 'current_net_balance'] as $col) {
                if (Schema::hasColumn('inventory_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
