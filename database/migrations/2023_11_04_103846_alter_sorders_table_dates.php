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
        Schema::table('sorders', function (Blueprint $table) {
            $table->timestamp('delivered_on')->nullable()->change();
            $table->timestamp('approved_on')->nullable()->change();
            $table->timestamp('supplied_on')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sorders', function (Blueprint $table) {
            //
        });
    }
};
