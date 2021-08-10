<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoreAttachmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_attachment', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('uid')->unsigned();
			$table->string('filename');
			$table->string('attachment');
			$table->boolean('type');
			$table->integer('createtime')->unsigned();
			$table->string('module_upload_dir', 100);
			$table->integer('group_id');
			$table->integer('displayorder');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('core_attachment');
	}

}
