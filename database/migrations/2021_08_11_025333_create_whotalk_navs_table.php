<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkNavsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_navs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->string('name')->default('')->comment('标题');
			$table->string('cid')->default('')->comment('位置');
			$table->string('type')->default('')->comment('类型');
			$table->string('extra')->default('')->comment('额外样式');
			$table->string('icon')->default('')->comment('图标');
			$table->string('url')->default('')->comment('链接');
			$table->boolean('inner')->default(0)->comment('内部');
			$table->boolean('status')->default(1)->comment('状态');
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
		Schema::drop('whotalk_navs');
	}

}
