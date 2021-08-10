<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUniAccountMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uni_account_menus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('menuid')->unsigned()->index('menuid');
			$table->boolean('type');
			$table->string('title', 30);
			$table->boolean('sex');
			$table->integer('group_id');
			$table->boolean('client_platform_type');
			$table->string('area', 50);
			$table->text('data', 65535);
			$table->boolean('status');
			$table->integer('createtime')->unsigned();
			$table->boolean('isdeleted');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uni_account_menus');
	}

}
