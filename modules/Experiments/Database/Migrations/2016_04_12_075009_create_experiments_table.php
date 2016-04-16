<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiments', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('device_id')->unsigned();
            $table->integer('software_id')->unsigned();
            $table->boolean('available')->default(false);

            $table->foreign('device_id')
            ->references('id')
            ->on('devices')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            
            $table->foreign('software_id')
            ->references('id')
            ->on('softwares')
            ->onDelete('cascade')
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
        Schema::drop('experiments');
    }

}
