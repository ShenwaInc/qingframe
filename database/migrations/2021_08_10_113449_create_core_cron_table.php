<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoreCronTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_cron', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('cloudid')->unsigned()->index('cloudid');
			$table->string('module', 50);
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->boolean('type');
			$table->string('name', 50);
			$table->string('filename', 50);
			$table->integer('lastruntime')->unsigned();
			$table->integer('nextruntime')->unsigned()->index('nextruntime');
			$table->boolean('weekday');
			$table->boolean('day');
			$table->boolean('hour');
			$table->string('minute');
			$table->string('extra', 5000);
			$table->boolean('status');
			$table->integer('createtime')->unsigned()->index('createtime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('core_cron');
	}

}
