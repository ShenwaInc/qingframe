<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticleNewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('article_news', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('cateid')->unsigned()->index('cateid');
			$table->string('title', 100)->index('title');
			$table->text('content', 16777215);
			$table->string('thumb');
			$table->string('source');
			$table->string('author', 50);
			$table->boolean('displayorder');
			$table->boolean('is_display');
			$table->boolean('is_show_home');
			$table->integer('createtime')->unsigned();
			$table->integer('click')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('article_news');
	}

}
