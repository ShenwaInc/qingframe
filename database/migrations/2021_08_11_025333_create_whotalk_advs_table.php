<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkAdvsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_advs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->string('title')->comment('标题');
			$table->string('location')->comment('显示位置');
			$table->string('type')->comment('广告类型');
			$table->string('pic')->comment('广告图片');
			$table->string('url')->comment('跳转链接');
			$table->text('content', 65535)->comment('广告内容');
			$table->string('platform')->default('')->comment('平台标识');
			$table->integer('status')->default(0)->comment('状态');
			$table->integer('displayorder')->default(0)->comment('显示顺序');
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
		Schema::drop('whotalk_advs');
	}

}
