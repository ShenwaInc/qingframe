<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkCashlogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_cashlog', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('用户UID');
			$table->string('nickname')->comment('用户OPENID');
			$table->string('openid')->comment('用户OPENID');
			$table->string('tid')->comment('交易号');
			$table->decimal('amount', 10)->default(0.00)->comment('金额');
			$table->integer('proportion')->default(0)->comment('抽成比例');
			$table->decimal('actual', 10)->default(0.00)->comment('实际到账');
			$table->string('method')->comment('提现方式');
			$table->text('content', 65535)->comment('配置信息');
			$table->boolean('paid')->default(0)->comment('已支付');
			$table->boolean('status')->default(1)->comment('状态');
			$table->integer('audittime')->default(0)->comment('审核时间');
			$table->string('remark')->comment('备注');
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
		Schema::drop('whotalk_cashlog');
	}

}
