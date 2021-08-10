<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoreResourceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_resource', function(Blueprint $table)
		{
			$table->integer('mid', true);
			$table->integer('uniacid')->unsigned()->index('acid');
			$table->string('media_id', 100);
			$table->integer('trunk')->unsigned();
			$table->string('type', 10)->index('type');
			$table->integer('dateline')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('core_resource');
	}

}
