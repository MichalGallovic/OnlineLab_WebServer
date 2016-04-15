<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExperimentServerTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experiment_server', function(Blueprint $table)
        {
            $table->increments('id');
            
            $table->integer('server_id')->unsigned();
            $table->integer('experiment_id')->unsigned();

            $table->foreign('server_id')
            ->references('id')
            ->on('servers')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->foreign('experiment_id')
            ->references('id')
            ->on('experiments')
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
        Schema::drop('experiment_server');
    }

}
