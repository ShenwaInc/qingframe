<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteStoreGoodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_store_goods', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type');
			$table->string('title', 100);
			$table->string('module', 50)->index('module');
			$table->integer('account_num');
			$table->integer('wxapp_num');
			$table->decimal('price', 10)->index('price');
			$table->string('unit', 15);
			$table->string('slide', 1000);
			$table->integer('category_id')->index('category_id');
			$table->string('title_initial', 1);
			$table->boolean('status');
			$table->integer('createtime');
			$table->string('synopsis');
			$table->text('description', 65535);
			$table->integer('module_group');
			$table->integer('api_num');
			$table->string('user_group_price', 1000);
			$table->integer('user_group');
			$table->integer('account_group');
			$table->boolean('is_wish');
			$table->string('logo', 300);
			$table->integer('platform_num');
			$table->integer('aliapp_num');
			$table->integer('baiduapp_num');
			$table->integer('phoneapp_num');
			$table->integer('toutiaoapp_num');
			$table->integer('webapp_num');
			$table->integer('xzapp_num');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_store_goods');
	}

}
