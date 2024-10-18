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
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('site_id');
            $table->unsignedBigInteger('location_id');
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('site_id')->references('id')->on('sites');
            $table->foreign('location_id')->references('id')->on('locations');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
