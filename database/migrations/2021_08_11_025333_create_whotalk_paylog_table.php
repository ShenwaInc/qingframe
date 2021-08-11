<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkPaylogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_paylog', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('用户UID');
			$table->string('nickname')->comment('用户昵称');
			$table->string('openid')->comment('用户OPENID');
			$table->string('tid')->comment('交易号');
			$table->string('plugin')->comment('相关插件');
			$table->decimal('amount', 10)->default(0.00)->comment('金额');
			$table->decimal('commission', 10)->default(0.00)->comment('分销佣金');
			$table->decimal('actual', 10)->default(0.00)->comment('订单原价');
			$table->decimal('deduct', 10)->default(0.00)->comment('抵扣金额');
			$table->decimal('discount', 2, 1)->default(0.0)->comment('折扣');
			$table->decimal('profit', 10)->default(0.00)->comment('净利润');
			$table->boolean('status')->default(1)->comment('状态');
			$table->integer('paytime')->default(0)->comment('支付时间');
			$table->text('setting', 65535)->comment('配置信息');
			$table->text('remark', 16777215)->comment('备注信息');
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
		Schema::drop('whotalk_paylog');
	}

}
