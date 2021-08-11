<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkPermTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_perm', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0);
			$table->integer('maxuser')->default(0);
			$table->integer('maxgroup')->default(0);
			$table->integer('maxchannel')->default(0);
			$table->integer('level')->default(0);
			$table->integer('overdue')->default(0);
			$table->text('plugins', 16777215);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('whotalk_perm');
	}

}
