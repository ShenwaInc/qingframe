<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoreQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_queue', function(Blueprint $table)
		{
			$table->bigInteger('qid', true)->unsigned();
			$table->integer('uniacid')->unsigned();
			$table->integer('acid')->unsigned();
			$table->string('message', 2000);
			$table->string('params', 1000);
			$table->string('keyword', 1000);
			$table->string('response', 2000);
			$table->string('module', 50)->index('module');
			$table->boolean('type');
			$table->integer('dateline')->unsigned()->index('dateline');
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
		Schema::drop('core_queue');
	}

}
