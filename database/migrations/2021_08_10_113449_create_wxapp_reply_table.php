<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWxappReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wxapp_reply', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('rid')->unsigned()->index('rid');
			$table->string('title', 50);
			$table->string('appid', 50);
			$table->string('pagepath');
			$table->string('mediaid', 50);
			$table->integer('createtime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wxapp_reply');
	}

}
