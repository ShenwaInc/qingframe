<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWxappRegisterVersionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wxapp_register_version', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->index('uniacid');
			$table->integer('version_id')->index('version_id');
			$table->integer('auditid')->index('auditid');
			$table->string('version', 20);
			$table->string('description');
			$table->boolean('status');
			$table->string('reason', 1000);
			$table->integer('upload_time');
			$table->text('audit_info', 65535);
			$table->text('submit_info', 65535);
			$table->string('developer', 100);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wxapp_register_version');
	}

}
