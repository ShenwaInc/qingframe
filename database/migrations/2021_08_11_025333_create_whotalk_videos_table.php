<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWhotalkVideosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('whotalk_videos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->default(0)->comment('公众号id');
			$table->integer('uid')->default(0)->comment('用户UID');
			$table->string('openid')->default('')->comment('用户OPENID');
			$table->string('name')->default('')->comment('文件名');
			$table->string('poster')->default('')->comment('视频封面');
			$table->string('videourl')->default('')->comment('视频路径');
			$table->string('videohd')->default('')->comment('高清路径');
			$table->string('videosd')->default('')->comment('标清路径');
			$table->integer('size')->default(0)->comment('文件大小');
			$table->boolean('status')->default(1);
			$table->integer('addtime')->default(0);
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
		Schema::drop('whotalk_videos');
	}

}
