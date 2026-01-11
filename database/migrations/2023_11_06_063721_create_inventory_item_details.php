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
        if (!Schema::hasTable('inventory_item_details')) {
            Schema::create('inventory_item_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('description')->nullable();
            $table->string('part_number')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('codes')->nullable();
            $table->string('uom')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('unit_cost_exc_vat_gh', 10, 2)->nullable();
            $table->decimal('unit_cost_exc_vat_usd', 10, 2)->nullable();
            $table->decimal('total_value_gh', 10, 2)->nullable();
            $table->decimal('total_value_usd', 10, 2)->nullable();
            $table->string('srf')->nullable();
            $table->string('erf')->nullable();
            $table->string('ats')->nullable();
            $table->string('drq')->nullable();
            $table->string('remarks')->nullable();
            $table->decimal('discount',10,2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            
            // $table->decimal('amount')->storedAs('unit_cost_exc_vat_gh * quantity')->nullable();
            $table->timestamps();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('inventory_id')->references('id')->on('inventories');
            $table->foreign('category_id')->references('id')->on('categories');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_item_details');
    }
};
