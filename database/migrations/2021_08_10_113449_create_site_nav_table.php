<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteNavTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_nav', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('multiid')->unsigned()->index('multiid');
			$table->boolean('section');
			$table->string('module', 50);
			$table->smallInteger('displayorder')->unsigned();
			$table->string('name', 50);
			$table->string('description', 1000);
			$table->boolean('position');
			$table->string('url');
			$table->string('icon', 500);
			$table->string('css', 1000);
			$table->boolean('status');
			$table->integer('categoryid')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_nav');
	}

}
