<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountXzappTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_xzapp', function(Blueprint $table)
		{
			$table->integer('acid')->primary();
			$table->integer('uniacid')->index('uniacid');
			$table->string('name');
			$table->string('original', 50);
			$table->integer('lastupdate');
			$table->integer('styleid');
			$table->integer('createtime');
			$table->string('token', 32);
			$table->string('encodingaeskey');
			$table->string('xzapp_id', 30);
			$table->boolean('level');
			$table->string('key', 80);
			$table->string('secret', 80);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('account_xzapp');
	}

}
