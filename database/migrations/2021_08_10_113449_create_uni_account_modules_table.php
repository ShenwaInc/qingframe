<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUniAccountModulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uni_account_modules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('idx_uniacid');
			$table->string('module', 50)->index('idx_module');
			$table->boolean('enabled');
			$table->text('settings', 65535);
			$table->boolean('shortcut');
			$table->integer('displayorder')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uni_account_modules');
	}

}
