<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkCommissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_commission', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('uniacid')->default(0)->comment('平台ID');
			$table->string('name', 20)->default('')->comment('名称');
			$table->smallInteger('commission')->default(0)->comment('一级佣金比例');
			$table->smallInteger('commission1')->default(0)->comment('二级佣金比例');
			$table->smallInteger('commission2')->default(0)->comment('三级佣金比例');
			$table->decimal('upgrade', 10)->default(0.00)->comment('升级条件');
			$table->boolean('isdefault')->default(0)->comment('是否默认');
			$table->boolean('status')->default(1)->comment('状态');
			$table->integer('dateline')->default(0)->comment('变更时间');
			$table->integer('addtime')->default(0)->comment('添加时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('whotalk_commission');
	}

}
