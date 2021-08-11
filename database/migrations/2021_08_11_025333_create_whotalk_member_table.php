<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkMemberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_member', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->unique('uid')->comment('用户UID');
			$table->string('userid', 20)->default('')->comment('自定义账号');
			$table->string('openid')->comment('用户OPENID');
			$table->string('gesture')->comment('手势密码');
			$table->string('paysecret', 20)->default('')->comment('支付权鉴');
			$table->integer('vipexpire')->default(0)->comment('到期时间');
			$table->boolean('inapp')->default(0)->comment('APP');
			$table->boolean('isrobot')->default(0)->comment('是否是机器人');
			$table->string('clientid')->comment('客户端标识');
			$table->string('clientip')->comment('客户端IP');
			$table->integer('agentid')->default(0)->comment('上级ID');
			$table->integer('cmid')->default(0)->comment('分销等级');
			$table->integer('follows')->default(0)->comment('关注量');
			$table->integer('praise')->default(0)->comment('点赞');
			$table->string('poslat', 20)->default('')->comment('定位纬度');
			$table->string('poslng', 20)->default('')->comment('定位经度');
			$table->text('setting', 16777215)->comment('用户设置');
			$table->boolean('hidden')->default(0)->comment('禁止被搜索');
			$table->boolean('status')->default(1)->comment('状态');
			$table->boolean('warned')->default(0)->comment('敏感信息');
			$table->string('remark')->comment('备注');
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
		Schema::drop('whotalk_member');
	}

}
