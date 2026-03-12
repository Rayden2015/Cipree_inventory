<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records that are still using the old default to Operational
        DB::table('endusers')
            ->whereNull('status')
            ->orWhere('status', 'Active')
            ->update(['status' => 'Operational']);

        // Adjust the column default at the DB level.
        // Use raw SQL to avoid requiring doctrine/dbal.
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `endusers` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT 'Operational'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE endusers ALTER COLUMN status SET DEFAULT 'Operational'");
        } else {
            // Fallback: rely on application-level defaults
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `endusers` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT 'Active'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE endusers ALTER COLUMN status SET DEFAULT 'Active'");
        }
    }
};

