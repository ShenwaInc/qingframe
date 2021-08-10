<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWxcardReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wxcard_reply', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('rid')->unsigned()->index('rid');
			$table->string('title', 30);
			$table->string('card_id', 50);
			$table->integer('cid')->unsigned();
			$table->string('brand_name', 30);
			$table->string('logo_url');
			$table->string('success');
			$table->string('error');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wxcard_reply');
	}

}
