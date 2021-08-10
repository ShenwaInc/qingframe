<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_templates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 30);
			$table->string('title', 30);
			$table->string('version', 64);
			$table->string('description', 500);
			$table->string('author', 50);
			$table->string('url');
			$table->string('type', 20);
			$table->integer('sections')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_templates');
	}

}
