<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModulesPluginRankTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules_plugin_rank', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid');
			$table->integer('uid');
			$table->integer('rank');
			$table->string('plugin_name', 200);
			$table->string('main_module_name', 200);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modules_plugin_rank');
	}

}
