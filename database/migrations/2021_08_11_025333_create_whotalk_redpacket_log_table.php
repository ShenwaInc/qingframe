<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkRedpacketLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_redpacket_log', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->default(0)->comment('公众号ID');
			$table->integer('pid')->default(0)->comment('产品ID');
			$table->integer('uid')->default(0)->comment('用户ID');
			$table->string('nickname')->default('')->comment('昵称');
			$table->decimal('amount', 10)->default(0.00)->comment('金额');
			$table->boolean('status')->default(0)->comment('状态');
			$table->string('remark')->default('')->comment('说明');
			$table->integer('addtime')->default(0)->comment('添加时间');
			$table->integer('dateline')->default(0)->comment('最后更新');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('whotalk_redpacket_log');
	}

}
