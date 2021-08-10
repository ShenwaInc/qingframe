<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoreCronRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_cron_record', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->string('module', 50)->index('module');
			$table->string('type', 50);
			$table->integer('tid')->unsigned()->index('tid');
			$table->string('note', 500);
			$table->string('tag', 5000);
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
		Schema::drop('core_cron_record');
	}

}
