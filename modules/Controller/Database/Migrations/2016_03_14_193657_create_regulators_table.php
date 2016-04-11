<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegulatorsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regulators', function(Blueprint $table)
        {
            $table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('system_id')->unsigned();
			$table->string('title');
			$table->text('body')->nullable();
			$table->enum('type', ['private', 'public', 'public_pending'])->default('private');
            $table->text('filename')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('regulators');
    }

}
