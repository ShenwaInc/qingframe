<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticleNoticeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('article_notice', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('cateid')->unsigned()->index('cateid');
			$table->string('title', 100)->index('title');
			$table->text('content', 16777215);
			$table->boolean('displayorder');
			$table->boolean('is_display');
			$table->boolean('is_show_home');
			$table->integer('createtime')->unsigned();
			$table->integer('click')->unsigned();
			$table->string('style', 200);
			$table->string('group');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('article_notice');
	}

}
