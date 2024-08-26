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
        Schema::table('endusers', function (Blueprint $table) {
            $table->unsignedBigInteger('enduser_category_id');
            $table->foreign('enduser_category_id')->references('id')->on('end_users_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('endusers', function (Blueprint $table) {
            //
        });
    }
};
