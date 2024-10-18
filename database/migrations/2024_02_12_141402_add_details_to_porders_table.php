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
            $table->string('po_number')->nullable();
            $table->string('suppliers_reference')->nullable();
            $table->timestamp('date_created')->nullable();
            $table->string('deliver_to')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();

            $table->foreign('site_id')->references('id')->on('sites');
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
