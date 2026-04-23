<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (! Schema::hasColumn('employees', 'login_user_id')) {
                    $table->unsignedBigInteger('login_user_id')->nullable()->after('user_id');
                    $table->foreign('login_user_id')->references('id')->on('users')->nullOnDelete();
                }
            });
        }

        // Backfill tenant_id for employees (required for TenantScope visibility)
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'tenant_id')) {
            $driver = DB::getDriverName();

            if ($driver === 'sqlite') {
                // SQLite doesn't support JOIN in UPDATE; use correlated subqueries.
                DB::statement("
                    UPDATE employees
                    SET tenant_id = (
                        SELECT tenant_id FROM sites WHERE sites.id = employees.site_id
                    )
                    WHERE tenant_id IS NULL
                      AND site_id IS NOT NULL
                      AND (
                        SELECT tenant_id FROM sites WHERE sites.id = employees.site_id
                      ) IS NOT NULL
                ");

                DB::statement("
                    UPDATE employees
                    SET tenant_id = (
                        SELECT tenant_id FROM users WHERE users.id = employees.user_id
                    )
                    WHERE tenant_id IS NULL
                      AND user_id IS NOT NULL
                      AND (
                        SELECT tenant_id FROM users WHERE users.id = employees.user_id
                      ) IS NOT NULL
                ");
            } else {
                // Prefer site->tenant_id where available
                DB::statement("
                    UPDATE employees e
                    JOIN sites s ON s.id = e.site_id
                    SET e.tenant_id = s.tenant_id
                    WHERE e.tenant_id IS NULL AND s.tenant_id IS NOT NULL
                ");

                // Fallback: user->tenant_id for any remaining rows
                DB::statement("
                    UPDATE employees e
                    JOIN users u ON u.id = e.user_id
                    SET e.tenant_id = u.tenant_id
                    WHERE e.tenant_id IS NULL AND u.tenant_id IS NOT NULL
                ");
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'login_user_id')) {
                // Drop FK if it exists (name differs by driver; try the conventional name)
                try {
                    $table->dropForeign(['login_user_id']);
                } catch (\Throwable $e) {
                    // ignore
                }
                $table->dropColumn('login_user_id');
            }
        });
    }
};

