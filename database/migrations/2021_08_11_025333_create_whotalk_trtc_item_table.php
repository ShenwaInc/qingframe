<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkTrtcItemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_trtc_item', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('创建者uid');
			$table->string('rid', 20)->default('')->comment('房间号');
			$table->string('mode', 20)->default('call')->comment('接入类型');
			$table->boolean('status')->default(1)->comment('状态');
			$table->integer('addtime')->default(0)->comment('添加时间');
			$table->integer('entrytime')->default(0)->comment('进入时间');
			$table->integer('exittime')->default(0)->comment('离开时间');
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
		Schema::drop('whotalk_trtc_item');
	}

}
