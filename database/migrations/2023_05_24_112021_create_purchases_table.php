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
        if (!Schema::hasTable('purchases')) {
            Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('part_id'); //order
            $table->string('description');
            $table->integer('quantity');
            $table->string('make');
            $table->string('model');
            $table->string('serial_number');
            $table->string('intended_recipient');
            $table->decimal('tax')->nullable();//order
            $table->decimal('tax2')->nullable();//order
            $table->decimal('tax3')->nullable();//order
            $table->decimal('unit_price');
            $table->string('currency');//order
            $table->unsignedBigInteger('supplier_id');
            $table->longText('comments')->nullable();
            $table->string('type_of_purchase');//order
            $table->unsignedBigInteger('enduser_id'); //intended recipient
            $table->string('status');//order
            $table->string('image')->nullable();//order
            $table->unsignedBigInteger('user_id'); //order
            $table->timestamps();

            $table->foreign('part_id')->references('id')->on('parts');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('enduser_id')->references('id')->on('endusers');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('purchases');
    }
};
