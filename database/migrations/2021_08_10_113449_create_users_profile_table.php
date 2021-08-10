<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersProfileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_profile', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uid')->unsigned();
			$table->integer('createtime')->unsigned();
			$table->integer('edittime');
			$table->string('realname', 10);
			$table->string('nickname', 20);
			$table->string('avatar');
			$table->string('qq', 15);
			$table->string('mobile', 11);
			$table->string('fakeid', 30);
			$table->boolean('vip');
			$table->boolean('gender');
			$table->smallInteger('birthyear')->unsigned();
			$table->boolean('birthmonth');
			$table->boolean('birthday');
			$table->string('constellation', 10);
			$table->string('zodiac', 5);
			$table->string('telephone', 15);
			$table->string('idcard', 30);
			$table->string('studentid', 50);
			$table->string('grade', 10);
			$table->string('address');
			$table->string('zipcode', 10);
			$table->string('nationality', 30);
			$table->string('resideprovince', 30);
			$table->string('residecity', 30);
			$table->string('residedist', 30);
			$table->string('graduateschool', 50);
			$table->string('company', 50);
			$table->string('education', 10);
			$table->string('occupation', 30);
			$table->string('position', 30);
			$table->string('revenue', 10);
			$table->string('affectivestatus', 30);
			$table->string('lookingfor');
			$table->string('bloodtype', 5);
			$table->string('height', 5);
			$table->string('weight', 5);
			$table->string('alipay', 30);
			$table->string('msn', 30);
			$table->string('email', 50);
			$table->string('taobao', 30);
			$table->string('site', 30);
			$table->text('bio', 65535);
			$table->text('interest', 65535);
			$table->string('workerid', 64);
			$table->boolean('is_send_mobile_status');
			$table->boolean('send_expire_status');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_profile');
	}

}
