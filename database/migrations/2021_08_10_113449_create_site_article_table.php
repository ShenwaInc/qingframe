<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteArticleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_article', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('rid')->unsigned();
			$table->boolean('iscommend')->index('idx_iscommend');
			$table->boolean('ishot')->index('idx_ishot');
			$table->integer('pcate')->unsigned();
			$table->integer('ccate')->unsigned();
			$table->string('template', 300);
			$table->string('title', 100);
			$table->string('description', 100);
			$table->text('content', 16777215);
			$table->string('thumb');
			$table->boolean('incontent');
			$table->string('source');
			$table->string('author', 50);
			$table->integer('displayorder')->unsigned();
			$table->string('linkurl', 500);
			$table->integer('createtime')->unsigned();
			$table->integer('edittime');
			$table->integer('click')->unsigned();
			$table->string('type', 10);
			$table->string('credit');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_article');
	}

}
