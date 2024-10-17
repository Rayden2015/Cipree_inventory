<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('sorders', function (Blueprint $table) {
        $table->index('site_id');          // Creates an index on the 'site_id' column
        $table->index('status');           // Creates an index on the 'status' column
        $table->index('delivered_on');     // Creates an index on the 'delivered_on' column
    });

    Schema::table('sorder_parts', function (Blueprint $table) {
        $table->index('site_id');          // Creates an index on the 'site_id' column
    });
}

public function down()
{
    Schema::table('sorders', function (Blueprint $table) {
        $table->dropIndex(['site_id']);    // Drops the index if you want to rollback
        $table->dropIndex(['status']);     // Drops the index on 'status'
        $table->dropIndex(['delivered_on']); // Drops the index on 'delivered_on'
    });

    Schema::table('sorder_parts', function (Blueprint $table) {
        $table->dropIndex(['site_id']);    // Drops the index on 'site_id'
    });
}

};
