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
        if (!Schema::hasTable('sorders')) {
            Schema::create('sorders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('tax')->nullable();
            $table->decimal('tax2')->nullable();
            $table->decimal('tax3')->nullable();
            $table->string('currency')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->date('request_date')->nullable();
            $table->string('type_of_purchase')->nullable();
            $table->string('request_number')->nullable();
            $table->string('purchasing_order_number')->nullable();
            $table->unsignedBigInteger('enduser_id')->nullable(); 
            $table->string('status')->nullable();
            $table->string('image')->nullable();
            $table->string('approval_status')->nullable();
            $table->string('delivery_reference_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('delivered_by')->nullable();
            $table->date('delivered_on')->nullable();
            $table->date('approved_on')->nullable();
            $table->date('supplied_on')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('inventory_id')->references('id')->on('inventories');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('enduser_id')->references('id')->on('endusers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('requested_by')->references('id')->on('users');
            // $table->foreign('delivered_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            // $table->unsignedBigInteger('inventory_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sorders');
    }
};
