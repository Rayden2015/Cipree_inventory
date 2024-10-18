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
        Schema::table('users', function (Blueprint $table) {
            $table->string('add_admin')->nullable();
            $table->string('add_site_admin')->nullable();
            $table->string('add_requester')->nullable();
            $table->string('add_finance_officer')->nullable();
            $table->string('add_store_officer')->nullable();
            $table->string('add_purchasing_officer')->nullable();
            $table->string('add_authoriser')->nullable();
            $table->string('add_store_assistant')->nullable();
            $table->string('add_procurement_assistant')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
