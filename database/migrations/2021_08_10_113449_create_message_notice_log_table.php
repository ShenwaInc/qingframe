<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageNoticeLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_notice_log', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('message');
			$table->boolean('is_read');
			$table->integer('uid');
			$table->string('sign', 22);
			$table->boolean('type');
			$table->boolean('status')->nullable();
			$table->integer('create_time');
			$table->integer('end_time');
			$table->string('url');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('message_notice_log');
	}

}
