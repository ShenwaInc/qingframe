<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountAliappTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_aliapp', function(Blueprint $table)
		{
			$table->integer('acid')->primary();
			$table->integer('uniacid');
			$table->boolean('level');
			$table->string('name', 30);
			$table->string('description');
			$table->string('key', 16);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('account_aliapp');
	}

}
