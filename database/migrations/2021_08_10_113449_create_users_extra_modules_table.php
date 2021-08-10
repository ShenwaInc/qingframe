<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersExtraModulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_extra_modules', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uid')->index('uid');
			$table->string('module_name', 100)->index('module_name');
			$table->string('support', 50);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_extra_modules');
	}

}
