<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRuleKeywordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rule_keyword', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('rid')->unsigned()->index('idx_rid');
			$table->integer('uniacid')->unsigned();
			$table->string('module', 50);
			$table->string('content')->index('idx_content');
			$table->boolean('type');
			$table->boolean('displayorder');
			$table->boolean('status');
			$table->index(['uniacid','type','content'], 'idx_uniacid_type_content');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rule_keyword');
	}

}
