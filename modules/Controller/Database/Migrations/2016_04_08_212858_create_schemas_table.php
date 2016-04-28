<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchemasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schemas', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title')->unique();
            $table->string('filename');
            $table->string('image')->nullable();
            $table->enum('type', ['file', 'text', 'none']);
            $table->enum('software', ['matlab', 'openmodelica', 'scilab']);
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
        Schema::drop('schemas');
    }

}
