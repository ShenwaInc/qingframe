<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersOperateHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_operate_history', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->index('type');
			$table->integer('uid')->unsigned()->index('uid');
			$table->integer('uniacid')->unsigned();
			$table->string('module_name', 100);
			$table->integer('createtime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_operate_history');
	}

}
