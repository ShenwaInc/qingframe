<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUniAccountModulesShortcutTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uni_account_modules_shortcut', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('title', 200);
			$table->string('url', 250);
			$table->string('icon', 200);
			$table->integer('uniacid');
			$table->integer('version_id');
			$table->string('module_name', 200);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uni_account_modules_shortcut');
	}

}
