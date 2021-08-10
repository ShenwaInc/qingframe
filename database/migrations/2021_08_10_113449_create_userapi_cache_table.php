<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserapiCacheTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('userapi_cache', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('key', 32);
			$table->text('content', 65535);
			$table->integer('lastupdate')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('userapi_cache');
	}

}
