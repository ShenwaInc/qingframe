<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGxswaCloudTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gxswa_cloud', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('identity', 50)->default('');
			$table->string('name', 50)->default('');
			$table->string('modulename', 50)->default('');
			$table->boolean('type')->default(0);
			$table->string('logo')->default('');
			$table->string('website')->default('');
			$table->string('rootpath', 50)->default('');
			$table->string('version', 20)->default('');
			$table->integer('releasedate')->default(0);
			$table->text('online', 16777215)->nullable();
			$table->integer('addtime')->default(0);
			$table->string('updatetime', 10)->default('0');
			$table->integer('dateline')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gxswa_cloud');
	}

}
