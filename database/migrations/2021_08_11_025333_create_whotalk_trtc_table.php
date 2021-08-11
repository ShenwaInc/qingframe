<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkTrtcTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_trtc', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('创建者uid');
			$table->string('roomid', 20)->default('')->comment('房间号');
			$table->boolean('audio')->default(0)->comment('语音');
			$table->boolean('video')->default(0)->comment('视频');
			$table->boolean('live')->default(0);
			$table->string('sid', 32)->default('')->comment('唯一标识');
			$table->boolean('status')->default(1)->comment('状态');
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
		Schema::drop('whotalk_trtc');
	}

}
