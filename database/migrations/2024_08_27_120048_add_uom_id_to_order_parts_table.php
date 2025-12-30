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
<<<<<<< HEAD
        if (! Schema::hasColumn('order_parts', 'uom_id')) {
        Schema::table('order_parts', function (Blueprint $table) {
            $table->unsignedBigInteger('uom_id')->nullable();
            });
        }

        if (Schema::hasColumn('order_parts', 'uom_id')) {
            Schema::table('order_parts', function (Blueprint $table) {
                if (Schema::hasTable('uom')) {
            $table->foreign('uom_id')->references('id')->on('uom');
                } elseif (Schema::hasTable('uoms')) {
                    $table->foreign('uom_id')->references('id')->on('uoms');
                }
        });
        }
=======
        // Only add the column and foreign key if the uoms table exists and column doesn't exist
        if (Schema::hasTable('uoms') && !Schema::hasColumn('order_parts', 'uom_id')) {
            Schema::table('order_parts', function (Blueprint $table) {
                $table->unsignedBigInteger('uom_id')->nullable();
                $table->foreign('uom_id')->references('id')->on('uoms');
            });
        }
>>>>>>> 8af09c4 (Add login banner, fix production error display, and suppress Carbon deprecation warnings)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<< HEAD
        if (Schema::hasColumn('order_parts', 'uom_id')) {
        Schema::table('order_parts', function (Blueprint $table) {
                try {
                    $table->dropForeign(['uom_id']);
                } catch (\Throwable $e) {
                    // Ignore if constraint does not exist.
                }
                $table->dropColumn('uom_id');
        });
=======
        // Only drop if the column exists
        if (Schema::hasColumn('order_parts', 'uom_id')) {
            Schema::table('order_parts', function (Blueprint $table) {
                // Drop foreign key first, then the column
                $table->dropForeign(['uom_id']);
                $table->dropColumn('uom_id');
            });
>>>>>>> 8af09c4 (Add login banner, fix production error display, and suppress Carbon deprecation warnings)
        }
    }
};
