<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('endusers')) {
            return;
        }

        Schema::table('endusers', function (Blueprint $table) {
            if (! Schema::hasColumn('endusers', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id');
                $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
                $table->unique('employee_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('endusers')) {
            return;
        }

        Schema::table('endusers', function (Blueprint $table) {
            if (Schema::hasColumn('endusers', 'employee_id')) {
                try {
                    $table->dropUnique(['employee_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                try {
                    $table->dropForeign(['employee_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('employee_id');
            }
        });
    }
};

