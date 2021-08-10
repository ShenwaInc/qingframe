<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteStoreCashOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_store_cash_order', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->char('number', 30);
			$table->integer('founder_uid')->index('founder_uid');
			$table->integer('order_id');
			$table->integer('goods_id');
			$table->decimal('order_amount', 10);
			$table->integer('cash_log_id');
			$table->boolean('status');
			$table->integer('create_time');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_store_cash_order');
	}

}
