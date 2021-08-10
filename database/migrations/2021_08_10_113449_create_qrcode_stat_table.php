<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQrcodeStatTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qrcode_stat', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('acid')->unsigned();
			$table->integer('qid')->unsigned();
			$table->string('openid', 50);
			$table->boolean('type');
			$table->bigInteger('qrcid')->unsigned();
			$table->string('scene_str', 64);
			$table->string('name', 50);
			$table->integer('createtime')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qrcode_stat');
	}

}
