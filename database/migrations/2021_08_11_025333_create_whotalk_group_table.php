<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_group', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->comment('公会名称');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('会长UID');
			$table->string('openid')->comment('会长OPENID');
			$table->string('nickname')->comment('会长昵称');
			$table->integer('level')->default(0)->comment('公会ID');
			$table->string('pic')->comment('频道封面');
			$table->string('description')->comment('公会简介');
			$table->text('notice', 16777215)->comment('公会公告');
			$table->integer('noticedate')->default(0)->comment('公告日期');
			$table->boolean('iscommon')->default(0)->comment('是否公共');
			$table->boolean('ischannel')->default(0)->comment('是否频道');
			$table->boolean('ishide')->nullable()->default(0)->comment('是否隐藏');
			$table->text('autoload', 16777215)->comment('自动更新规则');
			$table->boolean('allowpost')->default(1)->comment('允许发言');
			$table->boolean('allowjoin')->default(1)->comment('允许加入');
			$table->boolean('joinaudit')->default(0)->comment('需要审核');
			$table->boolean('protective')->default(0)->comment('成员保护');
			$table->decimal('fee', 10)->default(0.00)->comment('进群收费');
			$table->integer('credits')->default(0)->comment('公会积分');
			$table->integer('members')->default(0)->comment('成员数量');
			$table->integer('heats')->default(0)->comment('热度值');
			$table->boolean('status')->default(1)->comment('状态');
			$table->string('remark')->comment('备注');
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
		Schema::drop('whotalk_group');
	}

}
