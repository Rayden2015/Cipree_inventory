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
        if (!Schema::hasTable('porders')) {
            Schema::create('porders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('part_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('tax')->nullable();
            $table->decimal('tax2')->nullable();
            $table->decimal('tax3')->nullable();
            $table->string('currency')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->date('request_date')->nullable();
            $table->string('type_of_purchase');
            $table->string('request_number')->nullable();
            $table->string('purchasing_order_number')->nullable();
            $table->unsignedBigInteger('enduser_id'); //intended recipient
            $table->string('status');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('part_id')->references('id')->on('parts');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('enduser_id')->references('id')->on('endusers');
            $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('porders');
    }
};
