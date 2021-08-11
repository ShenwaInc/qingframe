<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_comment', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('用户UID');
			$table->string('nickname')->comment('用户OPENID');
			$table->integer('aid')->default(0)->comment('内容ID');
			$table->integer('cid')->default(0)->comment('评论ID');
			$table->string('type')->comment('类型');
			$table->string('pic')->comment('图片');
			$table->text('content', 16777215)->comment('内容');
			$table->boolean('warned')->default(0)->comment('敏感信息');
			$table->boolean('status')->default(1)->comment('状态');
			$table->integer('praise')->default(0)->comment('点赞量');
			$table->text('praiselog', 16777215)->nullable()->comment('点赞记录');
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
		Schema::drop('whotalk_comment');
	}

}
