<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWechatAttachmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wechat_attachment', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('acid')->unsigned()->index('acid');
			$table->integer('uid')->unsigned();
			$table->string('filename');
			$table->string('attachment');
			$table->string('media_id')->index('media_id');
			$table->integer('width')->unsigned();
			$table->integer('height')->unsigned();
			$table->string('type', 15);
			$table->string('model', 25);
			$table->string('tag', 5000);
			$table->integer('createtime')->unsigned();
			$table->string('module_upload_dir', 100);
			$table->integer('group_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wechat_attachment');
	}

}
