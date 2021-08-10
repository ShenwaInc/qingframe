<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersExtraLimitTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_extra_limit', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uid')->index('uid');
			$table->integer('maxaccount');
			$table->integer('maxwxapp');
			$table->integer('maxwebapp');
			$table->integer('maxphoneapp');
			$table->integer('maxxzapp');
			$table->integer('maxaliapp');
			$table->integer('timelimit');
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
		Schema::drop('users_extra_limit');
	}

}
