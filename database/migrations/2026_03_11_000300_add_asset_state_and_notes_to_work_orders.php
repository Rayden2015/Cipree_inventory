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
        Schema::table('work_orders', function (Blueprint $table) {
            // Current state of the asset as captured on this work order
            $table->string('asset_state', 50)
                ->default('Operational')
                ->after('priority');

            // When the asset went down (used for downtime calculations)
            $table->dateTime('asset_down_since')
                ->nullable()
                ->after('asset_state');

            // Free-text notes describing what work was actually performed
            $table->text('work_done_details')
                ->nullable()
                ->after('completed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['asset_state', 'asset_down_since', 'work_done_details']);
        });
    }
};

