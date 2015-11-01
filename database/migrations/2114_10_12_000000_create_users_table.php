<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('olm_admin_users', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('surname');
            $table->string('login');
            $table->string('language_code');

            $table->boolean('active')->default(1);

            $table->string('password', 60);
            $table->string('email')->unique();

            // Foreign keys
            $table->integer('account_type_id')->unsigned();
            $table->foreign('account_type_id')
                ->references('id')->on('olm_account_types');

            // Parameter deleted davame pravdepodobne prec
//            $table->boolean('deleted');

            $table->softDeletes();
            $table->rememberToken();
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
        Schema::drop('olm_admin_users');
    }
}
