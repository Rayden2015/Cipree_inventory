<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('id');
                $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
                $table->index('employee_id');
            }
        });

        // Backfill users.employee_id from employees.login_user_id
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'login_user_id')) {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                DB::statement("
                    UPDATE users
                    SET employee_id = (
                        SELECT e.id
                        FROM employees e
                        WHERE e.login_user_id = users.id
                        LIMIT 1
                    )
                    WHERE employee_id IS NULL
                ");
            } else {
                DB::statement("
                    UPDATE users u
                    JOIN employees e ON e.login_user_id = u.id
                    SET u.employee_id = e.id
                    WHERE u.employee_id IS NULL
                ");
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                try {
                    $table->dropIndex(['employee_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('employee_id');
            }
        });
    }
};

