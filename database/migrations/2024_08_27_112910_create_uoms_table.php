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
        if (!Schema::hasTable('uoms')) {
            Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol', 10)->nullable(); // Symbol of the unit (e.g., m, kg)
            $table->decimal('conversion_factor', 10, 4)->default(1.0)->nullable(); // Conversion factor to base unit
            $table->string('measurement_type')->nullable(); // Type of measurement (e.g., Length, Weight)
            $table->text('description')->nullable()->nullable(); // Optional description
            $table->boolean('is_default')->default(false)->nullable(); // Indicates if this is the default unit
            $table->boolean('base_unit')->default(false)->nullable(); // Specifies if this is the base unit for the measurement type
            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uoms');
    }
};
