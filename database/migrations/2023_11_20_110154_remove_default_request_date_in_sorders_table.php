<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('sorders', function (Blueprint $table) {
        //     $table->date('request_date')->default(null)->change();
        // });
        Schema::table('sorders', function (Blueprint $table) {
            DB::statement('ALTER TABLE `sorders`
            CHANGE `request_date` `request_date` TIMESTAMP NOT NULL DEFAULT NOW();');
           
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
