<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersLastuseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_lastuse', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uid');
			$table->integer('uniacid');
			$table->string('modulename', 100);
			$table->string('type', 100);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_lastuse');
	}

}
