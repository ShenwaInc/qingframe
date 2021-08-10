<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQrcodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('qrcode', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('acid')->unsigned();
			$table->string('type', 10);
			$table->integer('extra')->unsigned();
			$table->bigInteger('qrcid')->index('idx_qrcid');
			$table->string('scene_str', 64);
			$table->string('name', 50);
			$table->string('keyword', 100);
			$table->boolean('model');
			$table->string('ticket', 250)->index('ticket');
			$table->string('url', 256);
			$table->integer('expire')->unsigned();
			$table->integer('subnum')->unsigned();
			$table->integer('createtime')->unsigned();
			$table->boolean('status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('qrcode');
	}

}
