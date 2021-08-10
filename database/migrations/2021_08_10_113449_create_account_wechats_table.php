<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountWechatsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_wechats', function(Blueprint $table)
		{
			$table->integer('acid')->unsigned()->primary();
			$table->integer('uniacid')->unsigned();
			$table->string('token', 32);
			$table->string('encodingaeskey');
			$table->boolean('level');
			$table->string('name', 30);
			$table->string('account', 30);
			$table->string('original', 50);
			$table->string('signature', 100);
			$table->string('country', 10);
			$table->string('province', 3);
			$table->string('city', 15);
			$table->string('username', 30);
			$table->string('password', 32);
			$table->integer('lastupdate')->unsigned();
			$table->string('key', 50)->index('idx_key');
			$table->string('secret', 50);
			$table->integer('styleid')->unsigned();
			$table->string('subscribeurl', 120);
			$table->string('auth_refresh_token');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('account_wechats');
	}

}
