<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->string('codes');
            $table->string('part_number');
            $table->string('items');
            $table->string('uom');
            $table->string('category');
            $table->string('supplier');
            $table->decimal('dollar_rate', 10, 2);
            $table->decimal('unit_cost_exc_vat', 10, 2);
            $table->integer('reorder_point');
            $table->integer('qty_in_stocks');
            $table->string('status');
            $table->decimal('total_in_stock_in_val_gh', 10, 2);
            $table->decimal('total_in_stock_in_val_usd', 10, 2);
            $table->integer('total_out');
            $table->decimal('stock_out_val_gh', 10, 2);
            $table->decimal('stock_out_val_usd', 10, 2);
            $table->decimal('stock_balance_ext_gh', 10, 2);
            $table->decimal('stock_balance_ext_usd', 10, 2);
            $table->string('date');
            $table->string('aging_analysis');
            $table->string('po_number');
            $table->string('designation2');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}
