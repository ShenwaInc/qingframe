<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUniAccountUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uni_account_users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('uid')->unsigned()->index('uid');
			$table->string('role');
			$table->boolean('rank');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uni_account_users');
	}

}
