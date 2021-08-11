<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkPasscodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_passcode', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->default(0)->comment('公众号ID');
			$table->integer('pid')->default(0)->comment('产品ID');
			$table->integer('uid')->default(0)->comment('用户ID');
			$table->string('code', 50)->default('')->comment('卡密串');
			$table->decimal('price', 10)->default(0.00)->comment('售价');
			$table->decimal('deduct', 10)->default(0.00)->comment('抵扣金额');
			$table->boolean('status')->default(0)->comment('状态');
			$table->integer('usetime')->default(0)->comment('使用时间');
			$table->string('usefrom')->default('')->comment('使用地址');
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
		Schema::drop('whotalk_passcode');
	}

}
