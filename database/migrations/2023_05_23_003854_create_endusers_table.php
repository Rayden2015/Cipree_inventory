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
        if (!Schema::hasTable('endusers')) {
            Schema::create('endusers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('department');
            $table->string('section')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('bic')->nullable();
            $table->string('manufacturer')->nullable();


            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endusers');
    }
};
