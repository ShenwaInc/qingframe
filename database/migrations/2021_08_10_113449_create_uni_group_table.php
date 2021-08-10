<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUniGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uni_group', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('owner_uid');
			$table->string('name', 50);
			$table->text('modules', 65535);
			$table->string('templates', 5000);
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('uid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uni_group');
	}

}
