<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUniAccountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uni_account', function(Blueprint $table)
		{
			$table->increments('uniacid');
			$table->integer('groupid');
			$table->string('name', 100);
			$table->string('description');
			$table->integer('default_acid')->unsigned();
			$table->integer('rank')->nullable();
			$table->string('title_initial', 1);
			$table->integer('createtime');
			$table->string('logo');
			$table->string('qrcode');
			$table->integer('create_uid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uni_account');
	}

}
