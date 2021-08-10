<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_groups', function(Blueprint $table)
		{
			$table->integer('groupid', true);
			$table->integer('uniacid')->index('uniacid');
			$table->string('title', 20);
			$table->integer('credit')->unsigned();
			$table->boolean('isdefault');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_groups');
	}

}
