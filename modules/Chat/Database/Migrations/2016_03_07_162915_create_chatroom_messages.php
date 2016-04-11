<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatroomMessages extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatroom_messages', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('chatroom_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('body');


            $table->foreign(['user_id', 'chatroom_id'])
                ->references(['user_id', 'chatroom_id'])
                ->on('chatroom_permissions')
                ->onDelete('restrict')
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
        Schema::drop('chatroom_messages');
    }

}
