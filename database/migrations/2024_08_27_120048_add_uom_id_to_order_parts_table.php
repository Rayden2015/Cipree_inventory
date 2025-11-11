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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('order_parts', 'uom_id')) {
            Schema::table('order_parts', function (Blueprint $table) {
                try {
                    $table->dropForeign(['uom_id']);
                } catch (\Throwable $e) {
                    // Ignore if constraint does not exist.
                }
                $table->dropColumn('uom_id');
            });
        }
    }
};
