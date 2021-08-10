<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticleCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('article_comment', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('articleid')->index('articleid');
			$table->integer('parentid');
			$table->integer('uid');
			$table->string('content', 500)->nullable();
			$table->boolean('is_like');
			$table->boolean('is_reply');
			$table->integer('like_num');
			$table->integer('createtime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('article_comment');
	}

}
