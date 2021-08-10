<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatMsgHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stat_msg_history', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('rid')->unsigned();
			$table->integer('kid')->unsigned();
			$table->string('from_user', 50);
			$table->string('module', 50);
			$table->string('message', 1000);
			$table->string('type', 10);
			$table->integer('createtime')->unsigned()->index('idx_createtime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stat_msg_history');
	}

}
