<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkPluginTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_plugin', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('displayorder')->default(0)->comment('排序');
			$table->string('identity', 50)->default('')->comment('标识');
			$table->string('category')->default('')->comment('分类');
			$table->string('name', 50)->default('')->comment('名称');
			$table->string('version', 10)->default('')->comment('版本号');
			$table->string('author', 20)->default('')->comment('作者');
			$table->boolean('status')->default(0)->comment('状态');
			$table->string('thumb')->default('')->comment('头像');
			$table->string('summary', 500)->default('')->comment('简介');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('whotalk_plugin');
	}

}
