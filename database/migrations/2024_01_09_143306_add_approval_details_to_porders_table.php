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
        Schema::table('porders', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('approved_on')->nullable();
            $table->foreign('approved_by')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('porders', function (Blueprint $table) {
            //
        });
    }
};
