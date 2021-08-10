<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account', function(Blueprint $table)
		{
			$table->increments('acid');
			$table->integer('uniacid')->unsigned()->index('idx_uniacid');
			$table->string('hash', 8);
			$table->boolean('type');
			$table->boolean('isconnect');
			$table->boolean('isdeleted');
			$table->integer('endtime')->unsigned();
			$table->boolean('send_account_expire_status');
			$table->boolean('send_api_expire_status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('account');
	}

}
