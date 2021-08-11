<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkCommissionApplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_commission_apply', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('uniacid')->default(0)->comment('平台ID');
			$table->integer('uid')->default(0)->comment('用户UID');
			$table->string('nickname', 30)->default('')->comment('用户名');
			$table->string('qrcode')->default('')->comment('分销二维码');
			$table->text('profile', 16777215)->nullable()->comment('分销资料');
			$table->integer('cid')->default(0)->comment('分销等级');
			$table->string('remark')->default('')->comment('备注说明');
			$table->integer('shares')->default(0)->comment('分享次数');
			$table->boolean('status')->comment('状态');
			$table->integer('addtime')->default(0)->comment('添加时间');
			$table->integer('dateline')->default(0)->comment('变更时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('whotalk_commission_apply');
	}

}
