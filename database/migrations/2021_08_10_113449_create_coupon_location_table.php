<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCouponLocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('coupon_location', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('acid')->unsigned();
			$table->integer('sid')->unsigned();
			$table->integer('location_id')->unsigned();
			$table->string('business_name', 50);
			$table->string('branch_name', 50);
			$table->string('category');
			$table->string('province', 15);
			$table->string('city', 15);
			$table->string('district', 15);
			$table->string('address', 50);
			$table->string('longitude', 15);
			$table->string('latitude', 15);
			$table->string('telephone', 20);
			$table->string('photo_list', 10000);
			$table->integer('avg_price')->unsigned();
			$table->string('open_time', 50);
			$table->string('recommend');
			$table->string('special');
			$table->string('introduction');
			$table->boolean('offset_type');
			$table->boolean('status');
			$table->string('message');
			$table->index(['uniacid','acid'], 'uniacid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('coupon_location');
	}

}
