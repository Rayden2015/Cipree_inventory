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
        // Only add the column and foreign key if the uoms table exists and column doesn't exist
        if (Schema::hasTable('uoms') && !Schema::hasColumn('items', 'uom_id')) {
            Schema::table('items', function (Blueprint $table) {
                $table->unsignedBigInteger('uom_id')->nullable();
                $table->foreign('uom_id')->references('id')->on('uoms');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if the column exists
        if (Schema::hasColumn('items', 'uom_id')) {
            Schema::table('items', function (Blueprint $table) {
                try {
                    // Drop foreign key first, then the column
                    $table->dropForeign(['uom_id']);
                } catch (\Throwable $e) {
                    // Foreign key might not exist, ignore.
                }
                $table->dropColumn('uom_id');
            });
        }
    }
};
