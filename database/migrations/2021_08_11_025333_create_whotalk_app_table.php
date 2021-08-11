<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkAppTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_app', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('appid')->default(0)->comment('APPID');
			$table->string('appsecret')->default('')->comment('站点链接');
			$table->string('mobile', 20)->default('')->comment('手机号');
			$table->string('name')->default('')->comment('APP名称');
			$table->string('identity')->default('')->comment('APP包名');
			$table->string('version')->default('')->comment('APP版本号');
			$table->string('bale', 20)->default('')->comment('打包方式');
			$table->string('ios')->default('')->comment('iOS下载地址');
			$table->string('android')->default('')->comment('安卓下载地址');
			$table->text('data', 65535)->nullable()->comment('配置信息');
			$table->string('payurl')->default('')->comment('支付地址');
			$table->boolean('status')->default(0)->comment('状态');
			$table->string('remark')->default('')->comment('备注');
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
		Schema::drop('whotalk_app');
	}

}
