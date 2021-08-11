<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkRankingNearTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_ranking_near', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('UID');
			$table->string('cover')->default('')->comment('封面');
			$table->text('pics', 16777215)->nullable()->comment('图片');
			$table->integer('gender')->default(0)->comment('性别');
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
		Schema::drop('whotalk_ranking_near');
	}

}
