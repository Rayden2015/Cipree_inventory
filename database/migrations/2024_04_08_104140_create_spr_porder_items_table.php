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
        Schema::create('spr_porder_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spr_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('sorder_id')->nullable();
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
            $table->decimal('sub_total')->nullable();
          
            $table->decimal('discount')->nullable();
            $table->string('uom')->nullable();
            $table->decimal('rate')->nullable();
            $table->decimal('grand_total')->nullable();
            $table->decimal('price')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->integer('qty_supplied')->nullable();
            $table->timestamps();

            $table->foreign('spr_id')->references('id')->on('stock_purchase_requests')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('sorder_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('inventory_id')->references('id')->on('inventories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spr_porder_items');
    }
};
