<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules', function(Blueprint $table)
		{
			$table->increments('mid');
			$table->string('name', 100)->index('idx_name');
			$table->boolean('application_type');
			$table->string('type', 20);
			$table->string('title', 100);
			$table->string('version', 15);
			$table->string('ability', 500);
			$table->string('description', 1000);
			$table->string('author', 50);
			$table->string('url');
			$table->boolean('settings');
			$table->string('subscribes', 500);
			$table->string('handles', 500);
			$table->boolean('isrulefields');
			$table->boolean('issystem');
			$table->integer('target')->unsigned();
			$table->boolean('iscard');
			$table->string('permissions', 5000);
			$table->string('title_initial', 1);
			$table->boolean('wxapp_support');
			$table->integer('welcome_support');
			$table->boolean('oauth_type');
			$table->boolean('webapp_support');
			$table->boolean('phoneapp_support');
			$table->boolean('account_support');
			$table->boolean('xzapp_support');
			$table->boolean('aliapp_support');
			$table->string('logo', 250);
			$table->boolean('baiduapp_support');
			$table->boolean('toutiaoapp_support');
			$table->string('from', 10);
			$table->boolean('cloud_record');
			$table->integer('sections')->unsigned();
			$table->string('label', 500);
			$table->boolean('status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modules');
	}

}
