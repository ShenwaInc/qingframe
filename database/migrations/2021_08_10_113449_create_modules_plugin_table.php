<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModulesPluginTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules_plugin', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 100)->nullable()->index('name');
			$table->string('main_module', 100)->nullable()->index('main_module');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modules_plugin');
	}

}
