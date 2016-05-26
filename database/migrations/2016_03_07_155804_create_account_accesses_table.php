<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAccountAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_accesses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned();
            $table->string('ip');
            $table->string('os');
            $table->string('country');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('account_id')
                ->references('id')
                ->on('accounts');

        });
        DB::statement('ALTER TABLE account_accesses ADD location POINT' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('account_accesses');
    }
}
