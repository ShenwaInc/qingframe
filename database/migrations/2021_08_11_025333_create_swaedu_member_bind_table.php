<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSwaeduMemberBindTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('swaedu_member_bind', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('编号');
			$table->integer('uniacid')->default(0)->comment('平台ID');
			$table->integer('uid')->default(0)->comment('UID');
			$table->string('nickname', 50)->default('')->comment('平台昵称');
			$table->string('platform', 20)->default('')->comment('平台标识');
			$table->string('unionid', 100)->default('')->comment('unionid');
			$table->string('openid', 100)->default('')->comment('openid');
			$table->smallInteger('logins')->default(1)->comment('登录次数');
			$table->text('userinfo', 16777215)->nullable()->comment('用户资料');
			$table->boolean('status')->default(1)->comment('状态');
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
		Schema::drop('swaedu_member_bind');
	}

}
