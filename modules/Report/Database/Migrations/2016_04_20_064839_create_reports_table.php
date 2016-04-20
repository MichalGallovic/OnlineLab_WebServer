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
            $table->integer('server_id')->unsigned();
            $table->integer('experiment_id')->unsigned();
            $table->string("device_name");

            $table->text('input')->nullable();
            $table->text('output')->nullable();
            $table->text("notes")->nullable();

            $table->foreign("server_id")
            ->references('id')
            ->on("server_id")
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->foreing("experiment_id")
            ->references("id")
            ->on("experiment_id")
            ->onUpdate("cascade")
            ->onDelete("cascade");

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
