<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoNegativeValuesToInventoryItems extends Migration
{
    public function up()
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            // Add a check constraint to prevent negative quantity and amount
            DB::statement('ALTER TABLE inventory_items ADD CONSTRAINT chk_no_negative_values CHECK (quantity >= 0 AND amount >= 0)');
        });
    }

    public function down()
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            // Drop the check constraint if rolling back
            DB::statement('ALTER TABLE inventory_items DROP CONSTRAINT chk_no_negative_values');
        });
    }
}
