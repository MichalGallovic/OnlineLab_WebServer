<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('physical_experiment_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->text('input')->nullable();
            $table->text('output')->nullable();
            $table->text("notes")->nullable();
            $table->integer("simulation_time")->nullable();
            $table->integer("sampling_rate")->nullable();
            $table->boolean("filled")->default(false);

            $table->foreign("physical_experiment_id")
            ->references('id')
            ->on("physical_experiment")
            ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::drop('reports');
    }

}
