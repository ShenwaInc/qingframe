<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatVisitTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stat_visit', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->index('uniacid');
			$table->string('module', 100)->index('module');
			$table->integer('count')->unsigned();
			$table->integer('date')->unsigned()->index('date');
			$table->string('type', 10);
			$table->integer('ip_count');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stat_visit');
	}

}
