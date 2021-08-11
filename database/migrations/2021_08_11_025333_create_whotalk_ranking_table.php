<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkRankingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_ranking', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->string('name')->default('')->comment('排行名称');
			$table->string('summary')->default('')->comment('排行简介');
			$table->boolean('type')->default(0)->comment('排行类型');
			$table->string('orderby', 20)->default('')->comment('排序方式');
			$table->text('members', 65535)->nullable()->comment('用户信息');
			$table->boolean('homepage')->default(0)->comment('首页');
			$table->string('hometips')->default('')->comment('首页提示说明');
			$table->boolean('showdetail')->default(0)->comment('显示详情');
			$table->boolean('status')->default(0)->comment('状态');
			$table->string('remark')->default('')->comment('备注');
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
		Schema::drop('whotalk_ranking');
	}

}
