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
    if (!Schema::hasTable('update_inventory_item')) {
        Schema::create('update_inventory_item', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->text('before_InventoryItemDetail_edit')->nullable();
        $table->text('after_InventoryItemDetail_edit')->nullable();
        $table->decimal('new_amount', 15, 2);
        $table->timestamp('updated_at')->useCurrent();
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}

public function down()
{
    Schema::dropIfExists('update_inventory_item');
}

};
