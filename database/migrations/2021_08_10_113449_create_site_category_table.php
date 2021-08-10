<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_category', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('nid')->unsigned();
			$table->string('name', 50);
			$table->integer('parentid')->unsigned();
			$table->boolean('displayorder');
			$table->boolean('enabled');
			$table->string('icon', 100);
			$table->string('description', 100);
			$table->integer('styleid')->unsigned();
			$table->string('linkurl', 500);
			$table->boolean('ishomepage');
			$table->boolean('icontype');
			$table->string('css', 500);
			$table->integer('multiid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_category');
	}

}
