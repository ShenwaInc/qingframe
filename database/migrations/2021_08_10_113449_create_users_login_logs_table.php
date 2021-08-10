<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersLoginLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_login_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uid')->unsigned();
			$table->string('ip', 15);
			$table->string('city', 256);
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
		Schema::drop('users_login_logs');
	}

}
