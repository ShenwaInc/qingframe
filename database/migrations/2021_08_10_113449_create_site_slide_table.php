<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteSlideTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_slide', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('multiid')->unsigned()->index('multiid');
			$table->string('title');
			$table->string('url');
			$table->string('thumb');
			$table->boolean('displayorder');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_slide');
	}

}
