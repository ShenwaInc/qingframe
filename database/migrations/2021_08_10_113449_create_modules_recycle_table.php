<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModulesRecycleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules_recycle', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name');
			$table->boolean('type');
			$table->boolean('account_support');
			$table->boolean('wxapp_support');
			$table->boolean('welcome_support');
			$table->boolean('webapp_support');
			$table->boolean('phoneapp_support');
			$table->boolean('xzapp_support');
			$table->boolean('aliapp_support');
			$table->boolean('baiduapp_support');
			$table->boolean('toutiaoapp_support');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modules_recycle');
	}

}
