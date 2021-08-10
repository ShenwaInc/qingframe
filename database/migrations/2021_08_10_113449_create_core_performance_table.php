<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorePerformanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_performance', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type');
			$table->string('runtime', 10);
			$table->string('runurl', 512);
			$table->string('runsql', 512);
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
		Schema::drop('core_performance');
	}

}
