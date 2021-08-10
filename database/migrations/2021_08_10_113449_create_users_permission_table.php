<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersPermissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_permission', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('uid')->unsigned();
			$table->string('type', 100);
			$table->string('permission', 10000);
			$table->string('url');
			$table->text('modules', 65535);
			$table->text('templates', 65535);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_permission');
	}

}
