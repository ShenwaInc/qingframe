<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountBaiduappTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_baiduapp', function(Blueprint $table)
		{
			$table->integer('acid')->primary();
			$table->integer('uniacid')->index('uniacid');
			$table->string('name', 30);
			$table->string('appid', 32);
			$table->string('key', 32);
			$table->string('secret', 32);
			$table->string('description');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('account_baiduapp');
	}

}
