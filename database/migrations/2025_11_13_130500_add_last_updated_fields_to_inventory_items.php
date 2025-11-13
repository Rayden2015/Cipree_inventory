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
        Schema::table('inventory_items', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_items', 'last_updated_by')) {
                $table->unsignedBigInteger('last_updated_by')->nullable()->after('site_id');
            }

            if (! Schema::hasColumn('inventory_items', 'last_updated_at')) {
                $table->timestamp('last_updated_at')->nullable()->after('last_updated_by');
            }

            if (! Schema::hasColumn('inventory_items', 'total_value_gh')) {
                $table->decimal('total_value_gh', 15, 2)->nullable()->after('amount');
            }

            if (! Schema::hasColumn('inventory_items', 'total_value_usd')) {
                $table->decimal('total_value_usd', 15, 2)->nullable()->after('total_value_gh');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            foreach (['last_updated_by', 'last_updated_at'] as $column) {
                if (Schema::hasColumn('inventory_items', $column)) {
                    $table->dropColumn($column);
                }
            }
            // Do not drop total value columns if they existed previously
        });
    }
};
