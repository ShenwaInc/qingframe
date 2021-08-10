<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcHandselTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_handsel', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->index('uniacid');
			$table->integer('touid')->unsigned()->index('uid');
			$table->string('fromuid', 32);
			$table->string('module', 30);
			$table->string('sign', 100);
			$table->string('action', 20);
			$table->integer('credit_value')->unsigned();
			$table->integer('createtime')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_handsel');
	}

}
