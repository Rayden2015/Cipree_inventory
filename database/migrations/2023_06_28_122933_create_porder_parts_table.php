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
        Schema::create('porder_parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('part_id')->default(0)->nullable();
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
            $table->decimal('grand_total')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('porder_parts');
    }
};
