<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorePaylogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('core_paylog', function(Blueprint $table)
		{
			$table->bigInteger('plid', true)->unsigned();
			$table->string('type', 20);
			$table->integer('uniacid')->index('idx_uniacid');
			$table->integer('acid');
			$table->string('openid', 40)->index('idx_openid');
			$table->string('uniontid', 64)->index('uniontid');
			$table->string('tid', 128)->index('idx_tid');
			$table->decimal('fee', 10);
			$table->boolean('status');
			$table->string('module', 50);
			$table->string('tag', 2000);
			$table->boolean('is_usecard');
			$table->boolean('card_type');
			$table->string('card_id', 50);
			$table->decimal('card_fee', 10)->unsigned();
			$table->string('encrypt_code', 100);
			$table->boolean('is_wish');
			$table->string('coupon', 1000);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('core_paylog');
	}

}
