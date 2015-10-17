<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->timestamp('from');
            $table->timestamp('to');
            $table->timestamps();

            // $table->foreign('user_id')
            //       ->references('id')
            //       ->on('users')
            //       ->onUpdate('cascade');

            // $table->foreign('location_id')
            //       ->references('id')
            //       ->on('locations')
            //       ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('orders');
    }
}
