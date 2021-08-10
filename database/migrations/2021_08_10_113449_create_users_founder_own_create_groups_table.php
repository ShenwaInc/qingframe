<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersFounderOwnCreateGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_founder_own_create_groups', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('founder_uid')->index('founder_uid');
			$table->integer('create_group_id')->index('create_group_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_founder_own_create_groups');
	}

}
