<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_group', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('owner_uid');
			$table->string('name', 50);
			$table->string('package', 5000);
			$table->integer('maxaccount')->unsigned();
			$table->integer('timelimit')->unsigned();
			$table->integer('maxwxapp')->unsigned();
			$table->integer('maxwebapp');
			$table->integer('maxphoneapp');
			$table->integer('maxxzapp');
			$table->integer('maxaliapp');
			$table->integer('maxbaiduapp');
			$table->integer('maxtoutiaoapp');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_group');
	}

}
