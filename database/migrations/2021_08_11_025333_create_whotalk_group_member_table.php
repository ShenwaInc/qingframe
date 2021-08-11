<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkGroupMemberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_group_member', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('cid')->default(0)->comment('公会ID');
			$table->integer('uid')->default(0)->comment('UID');
			$table->string('nickname')->comment('昵称');
			$table->text('content', 16777215)->comment('说明');
			$table->integer('initmid')->default(0)->index('initmid')->comment('初始消息ID');
			$table->integer('lastmid')->default(0)->comment('最后阅读消息');
			$table->boolean('hide')->default(0)->comment('关闭会话');
			$table->integer('notalking')->default(0)->comment('禁言');
			$table->boolean('ismanager')->default(0)->comment('管理员');
			$table->boolean('status')->default(1)->comment('状态');
			$table->string('remark')->default('')->comment('备注');
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
		Schema::drop('whotalk_group_member');
	}

}
