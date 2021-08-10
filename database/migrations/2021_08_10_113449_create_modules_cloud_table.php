<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModulesCloudTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules_cloud', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 100)->index('name');
			$table->boolean('application_type');
			$table->string('title', 100);
			$table->string('title_initial', 1);
			$table->string('logo', 100);
			$table->string('version', 10);
			$table->boolean('install_status');
			$table->boolean('account_support');
			$table->boolean('wxapp_support');
			$table->boolean('webapp_support');
			$table->boolean('phoneapp_support');
			$table->boolean('welcome_support');
			$table->string('main_module_name', 50);
			$table->string('main_module_logo', 100);
			$table->boolean('has_new_version');
			$table->boolean('has_new_branch');
			$table->boolean('is_ban');
			$table->integer('lastupdatetime')->index('lastupdatetime');
			$table->boolean('xzapp_support');
			$table->integer('cloud_id');
			$table->boolean('aliapp_support');
			$table->boolean('baiduapp_support');
			$table->boolean('toutiaoapp_support');
			$table->integer('buytime');
			$table->boolean('module_status');
			$table->string('label', 500);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modules_cloud');
	}

}
