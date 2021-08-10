<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatKeywordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stat_keyword', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->string('rid', 10);
			$table->integer('kid')->unsigned();
			$table->integer('hit')->unsigned();
			$table->integer('lastupdate')->unsigned();
			$table->integer('createtime')->unsigned()->index('idx_createtime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stat_keyword');
	}

}
