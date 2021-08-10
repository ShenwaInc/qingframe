<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountWxappTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_wxapp', function(Blueprint $table)
		{
			$table->integer('acid')->unsigned()->primary();
			$table->integer('uniacid')->index('uniacid');
			$table->string('token', 32);
			$table->string('encodingaeskey', 43);
			$table->boolean('level');
			$table->string('account', 30);
			$table->string('original', 50);
			$table->string('key', 50);
			$table->string('secret', 50);
			$table->string('name', 30);
			$table->string('appdomain');
			$table->string('auth_refresh_token')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('account_wxapp');
	}

}
