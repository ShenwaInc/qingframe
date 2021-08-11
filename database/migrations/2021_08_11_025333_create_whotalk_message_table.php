<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_message', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('gid')->default(0)->index('gid');
			$table->integer('uid')->default(0)->index('uid');
			$table->integer('vid')->default(0)->comment('视频编号');
			$table->string('nickname')->default('');
			$table->string('openid')->default('');
			$table->integer('touid')->default(0)->index('touid');
			$table->boolean('highmode')->default(0)->comment('高级模式');
			$table->integer('at')->default(0);
			$table->integer('yuyintime')->default(0);
			$table->text('message', 16777215);
			$table->text('mediaid', 16777215)->comment('微信媒体资源ID');
			$table->boolean('status')->default(1);
			$table->boolean('warned')->default(0)->comment('敏感信息');
			$table->integer('addtime')->default(0)->index('addtime');
			$table->integer('dateline')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('whotalk_message');
	}

}
