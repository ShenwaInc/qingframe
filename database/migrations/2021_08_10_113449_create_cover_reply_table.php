<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoverReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cover_reply', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('multiid')->unsigned();
			$table->integer('rid')->unsigned()->index('rid');
			$table->string('module', 30);
			$table->string('do', 30);
			$table->string('title');
			$table->string('description');
			$table->string('thumb');
			$table->string('url');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cover_reply');
	}

}
