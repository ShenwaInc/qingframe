<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcFansTagTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_fans_tag', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->nullable();
			$table->integer('fanid')->index('fanid');
			$table->string('openid', 50)->index('openid');
			$table->integer('subscribe')->nullable();
			$table->string('nickname', 100)->nullable();
			$table->integer('sex')->nullable();
			$table->string('language', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('province', 50)->nullable();
			$table->string('country', 50)->nullable();
			$table->string('headimgurl', 150)->nullable();
			$table->integer('subscribe_time');
			$table->string('unionid', 100)->nullable();
			$table->string('remark', 250)->nullable();
			$table->string('groupid', 100)->nullable();
			$table->string('tagid_list', 250)->nullable();
			$table->string('subscribe_scene', 100)->nullable();
			$table->string('qr_scene_str', 250)->nullable();
			$table->string('qr_scene', 250)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_fans_tag');
	}

}
