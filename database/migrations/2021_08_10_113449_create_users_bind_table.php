<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersBindTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_bind', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uid')->index('uid');
			$table->string('bind_sign', 50)->index('bind_sign');
			$table->boolean('third_type');
			$table->string('third_nickname');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_bind');
	}

}
