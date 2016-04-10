<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatrooms extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatrooms', function(Blueprint $table)
        {
            $table->increments('id');
			$table->string('title');
            $table->timestamps();
            $table->enum('type', ['private', 'public_open', 'public_closed']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chatrooms');
    }

}
