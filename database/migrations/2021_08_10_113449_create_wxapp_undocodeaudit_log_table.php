<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWxappUndocodeauditLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wxapp_undocodeaudit_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->index('uniacid');
			$table->integer('version_id')->index('version_id');
			$table->integer('auditid')->index('auditid');
			$table->integer('revoke_time');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wxapp_undocodeaudit_log');
	}

}
