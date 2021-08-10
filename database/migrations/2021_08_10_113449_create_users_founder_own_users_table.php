<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersFounderOwnUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_founder_own_users', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uid')->index('uid');
			$table->integer('founder_uid')->index('founder_uid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_founder_own_users');
	}

}
