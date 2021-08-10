<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersFailedLoginTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_failed_login', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('ip', 15);
			$table->string('username', 32);
			$table->boolean('count');
			$table->integer('lastupdate')->unsigned();
			$table->index(['ip','username'], 'ip_username');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_failed_login');
	}

}
