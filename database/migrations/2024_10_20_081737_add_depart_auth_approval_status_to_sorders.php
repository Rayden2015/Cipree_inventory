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
            $table->String('depart_auth_approval_status')->nullable();
            $table->unsignedBigInteger('depart_auth_approved_by')->nullable();
            $table->unsignedBigInteger('depart_auth_denied_by')->nullable();
            $table->timestamp('depart_auth_approval_status_time')->nullable();
            $table->timestamp('depart_auth_approved_on')->nullable();
            $table->timestamp('depart_auth_denied_on')->nullable();
            // Optionally, add foreign key constraints if `depart_auth_approved_by` and `depart_auth_denied_by` reference another table (e.g., users)
             $table->foreign('depart_auth_approved_by')->references('id')->on('users');
             $table->foreign('depart_auth_denied_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sorders', function (Blueprint $table) {
            $table->dropColumn('depart_auth_approval_status');
            $table->dropColumn('depart_auth_approved_by');
            $table->dropColumn('depart_auth_denied_by');
            $table->dropColumn('depart_auth_approval_status_time');
        });
    }
};
