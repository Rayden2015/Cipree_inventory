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
        if (! Schema::hasColumn('sorders', 'work_order_number')) {
            Schema::table('sorders', function (Blueprint $table) {
                $table->string('work_order_number', 100)->nullable()->after('request_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sorders', 'work_order_number')) {
            Schema::table('sorders', function (Blueprint $table) {
                $table->dropColumn('work_order_number');
            });
        }
    }
};
