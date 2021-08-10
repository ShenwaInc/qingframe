<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('custom_reply', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('rid')->unsigned()->index('rid');
			$table->integer('start1');
			$table->integer('end1');
			$table->integer('start2');
			$table->integer('end2');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('custom_reply');
	}

}
