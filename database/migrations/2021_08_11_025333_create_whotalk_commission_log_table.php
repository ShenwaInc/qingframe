<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkCommissionLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_commission_log', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('uniacid')->default(0)->comment('平台ID');
			$table->integer('uid')->default(0)->comment('用户ID');
			$table->integer('fromuid')->default(0)->comment('类型');
			$table->string('type', 20)->default('')->comment('来源');
			$table->decimal('amout', 10)->default(0.00)->comment('消费金额');
			$table->decimal('commission', 10)->default(0.00)->comment('分销佣金');
			$table->smallInteger('rate')->default(0)->comment('佣金比例');
			$table->string('tid', 50)->default('')->comment('订单号');
			$table->string('settle', 10)->default('')->comment('佣金类型');
			$table->boolean('status')->default(0)->comment('状态');
			$table->integer('addtime')->default(0)->comment('添加时间');
			$table->integer('dateline')->default(0)->comment('更新时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('whotalk_commission_log');
	}

}
