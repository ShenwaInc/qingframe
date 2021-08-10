<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('uid');
			$table->integer('owner_uid');
			$table->integer('groupid')->unsigned();
			$table->boolean('founder_groupid');
			$table->string('username', 30)->unique('username');
			$table->string('password', 200);
			$table->string('salt', 10);
			$table->boolean('type');
			$table->boolean('status');
			$table->integer('joindate')->unsigned();
			$table->string('joinip', 15);
			$table->integer('lastvisit')->unsigned();
			$table->string('lastip', 15);
			$table->string('remark', 500);
			$table->integer('starttime')->unsigned();
			$table->integer('endtime')->unsigned();
			$table->boolean('register_type');
			$table->string('openid', 50);
			$table->boolean('welcome_link');
			$table->string('notice_setting', 5000);
			$table->boolean('is_bind');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
