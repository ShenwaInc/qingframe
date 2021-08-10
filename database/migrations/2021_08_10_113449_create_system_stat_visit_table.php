<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSystemStatVisitTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('system_stat_visit', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->index('uniacid');
			$table->string('modulename', 100);
			$table->integer('uid')->index('uid');
			$table->integer('displayorder');
			$table->integer('createtime');
			$table->integer('updatetime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('system_stat_visit');
	}

}
