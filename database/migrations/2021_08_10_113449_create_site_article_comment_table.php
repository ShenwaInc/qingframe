<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteArticleCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_article_comment', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->index('uniacid');
			$table->integer('articleid')->index('articleid');
			$table->integer('parentid');
			$table->integer('uid');
			$table->string('openid', 50);
			$table->text('content', 65535)->nullable();
			$table->boolean('is_read');
			$table->boolean('iscomment');
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
		Schema::drop('site_article_comment');
	}

}
