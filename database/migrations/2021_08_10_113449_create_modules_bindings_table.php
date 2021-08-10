<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModulesBindingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules_bindings', function(Blueprint $table)
		{
			$table->integer('eid', true);
			$table->string('module', 100)->index('idx_module');
			$table->string('entry', 30);
			$table->string('call', 50);
			$table->string('title', 50);
			$table->string('do', 200);
			$table->string('state', 200);
			$table->integer('direct');
			$table->string('url', 100);
			$table->string('icon', 50);
			$table->boolean('displayorder');
			$table->boolean('multilevel');
			$table->string('parent', 50);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modules_bindings');
	}

}
