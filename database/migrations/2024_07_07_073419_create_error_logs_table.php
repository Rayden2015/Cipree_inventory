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
        Schema::create('error_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Below is what are included in logger
            // you will know what it means later 
            $table->longText('message');
            $table->longText('context');
            $table->string('level')->index();
            $table->string('level_name');
            $table->string('channel')->index();
            $table->string('record_datetime');
            $table->longText('extra');
            $table->longText('formatted');
            // Additional custom fields I added 
            $table->string('remote_addr')->nullable();
            $table->string('user_agent')->nullable();
         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
