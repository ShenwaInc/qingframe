<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUniLinkUniacidTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uni_link_uniacid', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid');
			$table->integer('link_uniacid');
			$table->integer('version_id');
			$table->string('module_name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uni_link_uniacid');
	}

}
