<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoreJobTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_job', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->boolean('type');
			$table->integer('uniacid');
			$table->string('payload');
			$table->boolean('status');
			$table->string('title', 22);
			$table->integer('handled');
			$table->integer('total');
			$table->integer('createtime');
			$table->integer('updatetime');
			$table->integer('endtime');
			$table->integer('uid');
			$table->boolean('isdeleted')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('core_job');
	}

}
