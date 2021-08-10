<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcCreditsRechargeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_credits_recharge', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('uid')->unsigned();
			$table->string('openid', 50);
			$table->string('tid', 64)->index('idx_tid');
			$table->string('transid', 30);
			$table->string('fee', 10);
			$table->string('type', 15);
			$table->string('tag', 10);
			$table->boolean('status');
			$table->integer('createtime')->unsigned();
			$table->boolean('backtype');
			$table->index(['uniacid','uid'], 'idx_uniacid_uid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_credits_recharge');
	}

}
