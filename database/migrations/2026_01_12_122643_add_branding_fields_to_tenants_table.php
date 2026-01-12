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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('description');
            $table->string('primary_color', 7)->default('#007bff')->after('logo_path'); // Hex color code
            $table->string('secondary_color', 7)->nullable()->after('primary_color'); // Hex color code
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'primary_color', 'secondary_color']);
        });
    }
};
