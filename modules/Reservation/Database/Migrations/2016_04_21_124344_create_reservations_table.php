<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('physical_device_id')->unsigned();
            $table->dateTime("start");
            $table->dateTime("end");

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');

            $table->foreign("physical_device_id")
                ->references('id')
                ->on("physical_devices")
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reservations');
    }

}
