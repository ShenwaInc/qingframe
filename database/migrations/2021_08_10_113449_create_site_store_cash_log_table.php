<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteStoreCashLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_store_cash_log', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('founder_uid')->index('founder_uid');
			$table->char('number', 30);
			$table->decimal('amount', 10);
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
		Schema::drop('site_store_cash_log');
	}

}
