<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('location')->nullable();
            $table->string('tel')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('items_supplied')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('primary_currency')->nullable();
            $table->string('comp_reg_no')->nullable();

            $table->string('vat_reg_no')->nullable();

            $table->string('item_cat1')->nullable();
            $table->string('item_cat2')->nullable();
            $table->string('item_cat3')->nullable();


            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
};
