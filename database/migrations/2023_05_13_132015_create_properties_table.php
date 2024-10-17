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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->longText('description')->nullable();
            $table->string('location');
            $table->decimal('price');
            $table->string('property_type')->nullable();
            $table->string('status')->nullable();
            $table->string('area')->nullable();
            $table->integer('beds')->nullable();
            $table->integer('baths')->nullable();
            $table->integer('garage')->nullable();
            $table->string('balcony')->nullable();
            $table->string('deck')->nullable();
            $table->string('parking')->nullable();
            $table->string('outdoor_kitchen')->nullable();
            $table->string('tennis_court')->nullable();
            $table->string('sun_room')->nullable();
            $table->string('flat_tv')->nullable();
            $table->string('internet')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('state_id');
            $table->string('cover');
            $table->string('video')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('state_id')->references('id')->on('states');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
