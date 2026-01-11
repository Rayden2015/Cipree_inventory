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
        if (!Schema::hasTable('stock_purchase_request_items')) {
            Schema::create('stock_purchase_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sorder_id');
            $table->unsignedBigInteger('inventory_id')->nullable();
            $table->string('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('part_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->decimal('unit_price')->nullable()->default(0);
            $table->longText('comments')->nullable();
            $table->string('remarks')->nullable();
            $table->string('priority')->nullable();
            $table->string('prefix')->nullable();
            $table->string('purchasing_order_number')->nullable();
            $table->decimal('discount')->nullable();
            $table->decimal('sub_total',65,2)->nullable();
            $table->decimal('grand_total')->nullable();
            $table->decimal('price')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->integer('qty_supplied')->nullable();
            $table->timestamps();
           
            $table->foreign('item_id')->references('id')->on('items');

            $table->foreign('sorder_id')->references('id')->on('sorders')->onDelete('cascade');
            $table->foreign('inventory_id')->references('id')->on('inventories');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_purchase_request_items');
    }
};
