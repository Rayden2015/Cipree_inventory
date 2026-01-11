<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration handles old migrations that try to create tables that already exist.
     * It marks problematic migrations as run if the tables already exist.
     */
    public function up(): void
    {
        // List of old migrations that might try to create existing tables
        $problematicMigrations = [
            '2018_09_22_073526_create_sections_table' => 'sections',
            // Add more if needed
        ];

        foreach ($problematicMigrations as $migration => $tableName) {
            // Check if table exists
            if (Schema::hasTable($tableName)) {
                // Check if migration is already recorded
                $exists = DB::table('migrations')
                    ->where('migration', $migration)
                    ->exists();

                // If table exists but migration not recorded, mark it as run
                if (!$exists) {
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => DB::table('migrations')->max('batch') ?? 1,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the problematic migrations from migrations table
        DB::table('migrations')
            ->whereIn('migration', [
                '2018_09_22_073526_create_sections_table',
            ])
            ->delete();
    }
};
