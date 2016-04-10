<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatroomPermissions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatroom_permissions', function(Blueprint $table)
        {
			$table->integer('chatroom_id')->unsigned();
			$table->integer('user_id')->unsigned();
            $table->enum('type', ['creator', 'admin', 'member', 'spectator']);


            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('chatroom_id')
                ->references('id')
                ->on('chatrooms')
                ->onDelete('cascade')
                ->onUpdate('cascade');


            $table->primary(['user_id', 'chatroom_id']);
            $table->unique(['user_id', 'chatroom_id', 'type']);


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
        Schema::drop('chatroom_permissions');
    }

}
