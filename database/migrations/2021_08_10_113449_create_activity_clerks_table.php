<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityClerksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_clerks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('uid')->unsigned();
			$table->integer('storeid')->unsigned();
			$table->string('name', 20);
			$table->string('password', 20)->index('password');
			$table->string('mobile', 20);
			$table->string('openid', 50)->index('openid');
			$table->string('nickname', 30);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activity_clerks');
	}

}
