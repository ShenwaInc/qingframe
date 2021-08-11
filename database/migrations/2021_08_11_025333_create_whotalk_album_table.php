<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkAlbumTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_album', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('用户UID');
			$table->string('nickname')->default('')->comment('用户OPENID');
			$table->string('openid')->default('')->comment('用户OPENID');
			$table->string('avatar')->default('')->comment('用户OPENID');
			$table->integer('vid')->default(0)->comment('视频编号');
			$table->text('pics', 16777215)->comment('图片');
			$table->text('content', 16777215)->comment('内容');
			$table->string('tags')->default('')->comment('标签');
			$table->text('comments', 16777215)->comment('评论内容');
			$table->text('pralog', 16777215)->comment('点赞记录');
			$table->boolean('status')->default(1)->comment('状态');
			$table->boolean('warned')->default(0)->comment('敏感信息');
			$table->integer('views')->default(0)->comment('浏览量');
			$table->integer('praise')->default(0)->comment('点赞量');
			$table->integer('comment')->default(0)->comment('评论量');
			$table->integer('addtime')->default(0)->comment('添加时间');
			$table->integer('dateline')->default(0)->comment('最后变更时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('whotalk_album');
	}

}
