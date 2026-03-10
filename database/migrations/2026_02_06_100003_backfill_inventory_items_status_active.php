<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('inventory_items', 'status')) {
            DB::table('inventory_items')->whereNull('status')->orWhere('status', '')->update(['status' => 'Active']);
        }
    }

    public function down(): void
    {
        // No-op; backfill is data only
    }
};
