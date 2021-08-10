<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttachmentGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attachment_group', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('pid');
			$table->string('name', 25);
			$table->integer('uniacid');
			$table->integer('uid');
			$table->boolean('type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attachment_group');
	}

}
