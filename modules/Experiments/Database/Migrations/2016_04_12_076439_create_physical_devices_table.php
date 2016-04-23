<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhysicalDevicesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physical_devices', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('server_id')->unsigned();
            $table->integer('device_id')->unsigned();

            $table->string('name');
            $table->enum('status', ['offline','ready','experimenting'])->default('offline');

            $table->foreign('server_id')
            ->references('id')
            ->on('servers')
            ->onUpdate('cascade');

            $table->foreign('device_id')
            ->references('id')
            ->on('devices')
            ->onUpdate('cascade');            

            $table->softDeletes();
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
        Schema::drop('physical_devices');
    }

}
