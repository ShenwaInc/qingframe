<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcOauthFansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_oauth_fans', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('oauth_openid', 50);
			$table->integer('acid')->unsigned();
			$table->integer('uid')->unsigned();
			$table->string('openid', 50);
			$table->index(['oauth_openid','acid'], 'idx_oauthopenid_acid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_oauth_fans');
	}

}
