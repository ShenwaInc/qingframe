<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWxappVersionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wxapp_versions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('multiid')->unsigned();
			$table->string('version', 10)->index('version');
			$table->string('description');
			$table->string('modules', 1000);
			$table->boolean('design_method');
			$table->integer('template');
			$table->string('quickmenu', 2500);
			$table->integer('createtime');
			$table->integer('type');
			$table->integer('entry_id');
			$table->text('appjson', 65535);
			$table->text('default_appjson', 65535);
			$table->boolean('use_default');
			$table->string('last_modules', 1000)->nullable();
			$table->string('tominiprogram', 1000);
			$table->integer('upload_time');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wxapp_versions');
	}

}
