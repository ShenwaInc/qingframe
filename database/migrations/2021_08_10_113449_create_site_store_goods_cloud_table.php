<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteStoreGoodsCloudTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_store_goods_cloud', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('cloud_id')->unique('cloud_id');
			$table->string('name', 100);
			$table->string('title', 100);
			$table->string('logo', 300);
			$table->integer('wish_branch');
			$table->boolean('is_edited');
			$table->boolean('isdeleted');
			$table->string('branchs', 6000);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_store_goods_cloud');
	}

}
