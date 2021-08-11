<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkCollectionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_collection', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('用户UID');
			$table->string('openid')->default('')->comment('用户OPENID');
			$table->string('name')->default('')->comment('备注');
			$table->string('type')->default('')->comment('类型');
			$table->string('basic')->default('')->comment('来源');
			$table->string('comefrom')->default('')->comment('来源说明');
			$table->integer('tid')->default(0)->comment('内容编号');
			$table->text('poster', 16777215)->nullable()->comment('封面');
			$table->text('content', 16777215)->nullable()->comment('内容');
			$table->boolean('status')->default(1)->comment('状态');
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
		Schema::drop('whotalk_collection');
	}

}
