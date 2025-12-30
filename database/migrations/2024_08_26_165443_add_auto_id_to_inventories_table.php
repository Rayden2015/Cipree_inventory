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
<<<<<<< HEAD
        if (! Schema::hasColumn('inventories', 'id')) {
        Schema::table('inventories', function (Blueprint $table) {
                $table->bigIncrements('id')->first();
        });
=======
        // Only add the column if it doesn't already exist
        if (!Schema::hasColumn('inventories', 'id')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->bigIncrements('id')->first(); // Adds an auto-incrementing 'id' column
            });
>>>>>>> 8af09c4 (Add login banner, fix production error display, and suppress Carbon deprecation warnings)
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<< HEAD
        if (Schema::hasColumn('inventories', 'id')) {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('id');
        });
=======
        // Only drop the column if it exists
        if (Schema::hasColumn('inventories', 'id')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->dropColumn('id');
            });
>>>>>>> 8af09c4 (Add login banner, fix production error display, and suppress Carbon deprecation warnings)
        }
    }
};
