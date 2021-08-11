<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkFriendTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_friend', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('UID');
			$table->string('openid')->comment('OPENID');
			$table->integer('fuid')->default(0)->comment('好友UID');
			$table->string('note')->comment('说明');
			$table->string('letter', 2)->comment('首字母');
			$table->boolean('hide')->default(0)->comment('关闭会话');
			$table->boolean('status')->default(1)->comment('状态');
			$table->integer('apply')->default(0)->comment('申请状态');
			$table->integer('firstmid')->default(0)->comment('起始阅读消息');
			$table->integer('lastmid')->default(0)->comment('最后阅读消息');
			$table->string('remark')->comment('昵称');
			$table->integer('addtime')->default(0)->comment('添加时间');
			$table->integer('dateline')->default(0)->comment('发布时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('whotalk_friend');
	}

}
