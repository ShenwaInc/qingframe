<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersInvitationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_invitation', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 64)->unique('idx_code');
			$table->integer('fromuid')->unsigned();
			$table->integer('inviteuid')->unsigned();
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
		Schema::drop('users_invitation');
	}

}
