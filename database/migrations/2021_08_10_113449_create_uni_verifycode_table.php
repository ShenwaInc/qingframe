<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUniVerifycodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uni_verifycode', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->string('receiver', 50);
			$table->string('verifycode', 6);
			$table->boolean('total');
			$table->integer('createtime')->unsigned();
			$table->integer('failed_count')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uni_verifycode');
	}

}
