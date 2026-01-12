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
        Schema::table('items', function (Blueprint $table) {
            $table->integer('max_stock_level')->nullable()->after('reorder_level');
            $table->integer('lead_time_days')->nullable()->after('max_stock_level');
            $table->enum('valuation_method', ['FIFO', 'LIFO', 'Weighted Average'])->nullable()->after('lead_time_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['max_stock_level', 'lead_time_days', 'valuation_method']);
        });
    }
};
