<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNewsReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('news_reply', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('rid')->unsigned()->index('rid');
			$table->integer('parent_id');
			$table->string('title', 50);
			$table->string('author', 64);
			$table->string('description');
			$table->string('thumb', 500);
			$table->text('content', 16777215);
			$table->string('url');
			$table->integer('displayorder');
			$table->boolean('incontent');
			$table->integer('createtime');
			$table->string('media_id', 50);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('news_reply');
	}

}
