<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteStoreCreateAccountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_store_create_account', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uid');
			$table->integer('uniacid');
			$table->boolean('type');
			$table->integer('endtime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_store_create_account');
	}

}
