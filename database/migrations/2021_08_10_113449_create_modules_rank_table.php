<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModulesRankTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules_rank', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('module_name', 100)->index('module_name');
			$table->integer('uid')->index('uid');
			$table->integer('rank');
			$table->integer('uniacid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modules_rank');
	}

}
