<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMobilenumberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mobilenumber', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('rid');
			$table->boolean('enabled');
			$table->integer('dateline')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mobilenumber');
	}

}
