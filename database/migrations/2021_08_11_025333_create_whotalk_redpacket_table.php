<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkRedpacketTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_redpacket', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->default(0)->comment('公众号ID');
			$table->integer('gid')->default(0)->comment('群组');
			$table->integer('uid')->default(0)->comment('UID');
			$table->string('nickname', 50)->default('')->comment('昵称');
			$table->integer('touid')->default(0)->comment('对方UID');
			$table->boolean('type')->default(0)->comment('类型');
			$table->decimal('amount', 10)->default(0.00)->comment('总额');
			$table->decimal('balance', 10)->default(0.00)->comment('余额');
			$table->integer('counts')->default(0)->comment('总量');
			$table->integer('remain')->default(0)->comment('余量');
			$table->boolean('status')->default(0)->comment('状态');
			$table->string('remark')->default('')->comment('说明');
			$table->integer('addtime')->default(0)->comment('添加时间');
			$table->integer('paytime')->default(0)->comment('支付时间');
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
		Schema::drop('whotalk_redpacket');
	}

}
