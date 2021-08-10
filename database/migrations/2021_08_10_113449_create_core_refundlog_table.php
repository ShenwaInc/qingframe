<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoreRefundlogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_refundlog', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uniacid');
			$table->string('refund_uniontid', 64)->index('refund_uniontid');
			$table->string('reason', 80);
			$table->string('uniontid', 64)->index('uniontid');
			$table->decimal('fee', 10);
			$table->integer('status');
			$table->boolean('is_wish');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('core_refundlog');
	}

}
