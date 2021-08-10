<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcCashRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_cash_record', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->integer('uid')->unsigned()->index('uid');
			$table->integer('clerk_id')->unsigned();
			$table->integer('store_id')->unsigned();
			$table->boolean('clerk_type');
			$table->decimal('fee', 10)->unsigned();
			$table->decimal('final_fee', 10)->unsigned();
			$table->integer('credit1')->unsigned();
			$table->decimal('credit1_fee', 10)->unsigned();
			$table->decimal('credit2', 10)->unsigned();
			$table->decimal('cash', 10)->unsigned();
			$table->decimal('return_cash', 10)->unsigned();
			$table->decimal('final_cash', 10)->unsigned();
			$table->string('remark');
			$table->integer('createtime')->unsigned();
			$table->string('trade_type', 20);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_cash_record');
	}

}
