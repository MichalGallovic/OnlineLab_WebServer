<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('ip')->unique();
            $table->string('port');
            $table->boolean('production')->default(false);
            $table->boolean("disabled")->default(false);
            $table->boolean("database")->default(false);
            $table->boolean("reachable")->default(false);
            $table->string('color')->default("#009051");
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
        Schema::drop('servers');
    }

}
