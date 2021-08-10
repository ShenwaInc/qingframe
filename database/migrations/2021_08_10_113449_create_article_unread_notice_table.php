<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticleUnreadNoticeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('article_unread_notice', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('notice_id')->unsigned()->index('notice_id');
			$table->integer('uid')->unsigned()->index('uid');
			$table->boolean('is_new');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('article_unread_notice');
	}

}
