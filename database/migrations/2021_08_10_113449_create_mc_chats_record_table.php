<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcChatsRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_chats_record', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('acid')->unsigned();
			$table->boolean('flag');
			$table->string('openid', 32)->index('openid');
			$table->string('msgtype', 15);
			$table->string('content', 10000);
			$table->integer('createtime')->unsigned()->index('createtime');
			$table->index(['uniacid','acid'], 'uniacid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_chats_record');
	}

}
