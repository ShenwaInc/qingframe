<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoreSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_sessions', function(Blueprint $table)
		{
			$table->char('sid', 32)->primary();
			$table->integer('uniacid')->unsigned();
			$table->string('openid', 50);
			$table->string('data', 2000);
			$table->integer('expiretime')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('core_sessions');
	}

}
