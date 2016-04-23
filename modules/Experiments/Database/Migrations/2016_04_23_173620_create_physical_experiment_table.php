<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhysicalExperimentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physical_experiment', function(Blueprint $table)
        {
            $table->increments('id');
            
            $table->integer('server_id')->unsigned();
            $table->integer('experiment_id')->unsigned();
            $table->integer("physical_device_id")->unsigned();

            $table->text("commands")->nullable();
            $table->text("experiment_commands")->nullable();
            $table->text("output_arguments")->nullable();

            $table->foreign('server_id')
            ->references('id')
            ->on('servers')
            ->onUpdate('cascade');

            $table->foreign('experiment_id')
            ->references('id')
            ->on('experiments')
            ->onUpdate('cascade');

             $table->foreign('physical_device_id')
            ->references('id')
            ->on('physical_devices')
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
        Schema::drop('physical_experiment');
    }

}
