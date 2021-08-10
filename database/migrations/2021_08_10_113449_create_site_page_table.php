<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSitePageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_page', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('multiid')->unsigned()->index('multiid');
			$table->string('title', 50);
			$table->string('description');
			$table->text('params');
			$table->text('html');
			$table->text('multipage');
			$table->boolean('type');
			$table->boolean('status');
			$table->integer('createtime')->unsigned();
			$table->integer('goodnum')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_page');
	}

}
