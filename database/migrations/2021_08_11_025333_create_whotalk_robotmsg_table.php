<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkRobotmsgTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_robotmsg', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0);
			$table->integer('gid')->default(0);
			$table->integer('touid')->default(0);
			$table->boolean('highmode')->default(0)->comment('高级模式');
			$table->integer('at')->default(0);
			$table->text('message', 16777215)->nullable()->comment('消息内容');
			$table->integer('vid')->default(0)->comment('视频编号');
			$table->integer('yuyintime')->default(0)->comment('语音时长');
			$table->integer('mid')->default(0)->comment('消息编号');
			$table->string('session_id', 100)->default('')->comment('历史会话ID');
			$table->text('candidates', 16777215)->nullable()->comment('候选答复');
			$table->boolean('status')->default(1)->comment('状态');
			$table->string('remark')->default('')->comment('备注');
			$table->integer('sendtime')->default(0)->comment('发送时间');
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
		Schema::drop('whotalk_robotmsg');
	}

}
