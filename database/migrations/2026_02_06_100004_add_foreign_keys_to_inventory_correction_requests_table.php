<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add foreign keys only for InnoDB tables. This DB has several MyISAM
     * tables (inventory_items, users, sites); FKs are only added for
     * tenant_id and reason_code_id. Other IDs are application-enforced.
     */
    public function up(): void
    {
        Schema::table('inventory_correction_requests', function (Blueprint $table) {
            $table->foreign('reason_code_id')->references('id')->on('inventory_correction_reason_codes')->nullOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_correction_requests', function (Blueprint $table) {
            $table->dropForeign(['reason_code_id']);
            $table->dropForeign(['tenant_id']);
        });
    }
};
