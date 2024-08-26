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
        Schema::create('total_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_id');
            $table->decimal('sub_total',15,2)->nullable();
            $table->decimal('grand_total',15,2)->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();

            $table->foreign('tax_id')->references('id')->on('taxes');
            $table->foreign('modified_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('total_taxes');
    }
};
