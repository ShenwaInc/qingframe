<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersCreateGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_create_group', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('group_name', 50);
			$table->integer('maxaccount');
			$table->integer('maxwxapp');
			$table->integer('maxwebapp');
			$table->integer('maxphoneapp');
			$table->integer('maxxzapp');
			$table->integer('maxaliapp');
			$table->integer('createtime');
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
		Schema::drop('users_create_group');
	}

}
