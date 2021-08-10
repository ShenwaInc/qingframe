<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityClerkMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_clerk_menu', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid');
			$table->integer('displayorder');
			$table->integer('pid');
			$table->string('group_name', 20);
			$table->string('title', 20);
			$table->string('icon', 50);
			$table->string('url');
			$table->string('type', 20);
			$table->string('permission', 50);
			$table->integer('system');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activity_clerk_menu');
	}

}
