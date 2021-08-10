<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWechatNewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wechat_news', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->nullable()->index('uniacid');
			$table->integer('attach_id')->unsigned()->index('attach_id');
			$table->string('thumb_media_id', 60);
			$table->string('thumb_url');
			$table->string('title', 50);
			$table->string('author', 30);
			$table->string('digest');
			$table->text('content', 16777215);
			$table->string('content_source_url', 200);
			$table->boolean('show_cover_pic');
			$table->string('url', 200);
			$table->integer('displayorder');
			$table->boolean('need_open_comment');
			$table->boolean('only_fans_can_comment');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wechat_news');
	}

}
