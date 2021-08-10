<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteStoreOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_store_order', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('orderid', 28);
			$table->integer('goodsid')->index('goodid');
			$table->integer('duration');
			$table->string('buyer', 50);
			$table->integer('buyerid')->index('buyerid');
			$table->decimal('amount', 10);
			$table->boolean('type');
			$table->boolean('changeprice');
			$table->integer('createtime');
			$table->integer('uniacid');
			$table->integer('endtime');
			$table->integer('wxapp');
			$table->boolean('is_wish');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_store_order');
	}

}
