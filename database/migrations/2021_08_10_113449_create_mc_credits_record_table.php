<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcCreditsRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_credits_record', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uid')->unsigned()->index('uid');
			$table->integer('uniacid')->index('uniacid');
			$table->string('credittype', 10);
			$table->decimal('num', 10);
			$table->integer('operator')->unsigned();
			$table->string('module', 30);
			$table->integer('clerk_id')->unsigned();
			$table->integer('store_id')->unsigned();
			$table->boolean('clerk_type');
			$table->integer('createtime')->unsigned();
			$table->string('remark', 200);
			$table->integer('real_uniacid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_credits_record');
	}

}
