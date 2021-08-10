<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteStylesVarsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_styles_vars', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('templateid')->unsigned();
			$table->integer('styleid')->unsigned();
			$table->string('variable', 50);
			$table->text('content', 65535);
			$table->string('description', 50);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_styles_vars');
	}

}
