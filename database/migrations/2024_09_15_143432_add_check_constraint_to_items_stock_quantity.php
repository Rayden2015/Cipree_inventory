<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items_stock_quantity', function (Blueprint $table) {
            DB::statement('ALTER TABLE items ADD CONSTRAINT chk_stock_quantity_non_negative CHECK (stock_quantity >= 0)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items_stock_quantity', function (Blueprint $table) {
            DB::statement('ALTER TABLE items DROP CONSTRAINT chk_stock_quantity_non_negative');
        });
    }
};
