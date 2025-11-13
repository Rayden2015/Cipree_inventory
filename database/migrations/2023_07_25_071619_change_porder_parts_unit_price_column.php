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
        if (app()->environment('testing')) {
            return;
        }

        Schema::table('porder_parts', function($table) {
            $table->decimal('unit_price',65,2)->nullable()->default(0)->change();
            $table->decimal('sub_total',65,2)->nullable()->default(0)->change();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('testing')) {
            return;
        }
    }
};
