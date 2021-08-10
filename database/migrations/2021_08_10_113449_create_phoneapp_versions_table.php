<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePhoneappVersionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('phoneapp_versions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid')->index('uniacid');
			$table->string('version', 20)->index('version');
			$table->string('description');
			$table->text('modules', 65535);
			$table->integer('createtime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('phoneapp_versions');
	}

}
