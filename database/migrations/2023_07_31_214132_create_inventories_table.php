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
        if (!Schema::hasTable('inventories')) {
            Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('waybill')->nullable();
            $table->unsignedBigInteger('enduser_id')->nullable();
            $table->decimal('dollar_rate', 10, 2)->nullable();
            $table->string('po_number')->nullable();
            $table->string('grn_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('delivered_by')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('trans_type')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('billing_currency')->nullable();
            $table->timestamps();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('enduser_id')->references('id')->on('endusers');
            $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
