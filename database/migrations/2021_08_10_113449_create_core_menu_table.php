<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoreMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_menu', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pid')->unsigned();
			$table->string('title', 20);
			$table->string('name', 20);
			$table->string('url');
			$table->string('append_title', 30);
			$table->string('append_url');
			$table->boolean('displayorder');
			$table->string('type', 15);
			$table->boolean('is_display');
			$table->boolean('is_system');
			$table->string('permission_name', 50);
			$table->string('group_name', 30);
			$table->string('icon', 20);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('core_menu');
	}

}
