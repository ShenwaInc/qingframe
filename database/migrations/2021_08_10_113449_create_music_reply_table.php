<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMusicReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('music_reply', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('rid')->unsigned()->index('rid');
			$table->string('title', 50);
			$table->string('description');
			$table->string('url', 300);
			$table->string('hqurl', 300);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('music_reply');
	}

}
