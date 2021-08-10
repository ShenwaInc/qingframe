<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBusinessTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('business', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('weid')->unsigned();
			$table->string('title', 50);
			$table->string('thumb');
			$table->string('content', 1000);
			$table->string('phone', 15);
			$table->string('qq', 15);
			$table->string('province', 50);
			$table->string('city', 50);
			$table->string('dist', 50);
			$table->string('address', 500);
			$table->string('lng', 10);
			$table->string('lat', 10);
			$table->string('industry1', 10);
			$table->string('industry2', 10);
			$table->integer('createtime');
			$table->index(['lng','lat'], 'idx_lat_lng');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('business');
	}

}
