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
        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_description')->nullable();
            $table->string('item_uom')->nullable();
            $table->string('item_part_number')->nullable();
            $table->string('item_stock_code')->unique();
            $table->unsignedBigInteger('item_category_id')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->foreign('item_category_id')->references('id')->on('categories');
            $table->foreign('added_by')->references('id')->on('users');
            $table->foreign('modified_by')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
