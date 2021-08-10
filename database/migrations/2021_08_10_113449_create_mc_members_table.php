<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_members', function(Blueprint $table)
		{
			$table->increments('uid');
			$table->integer('uniacid')->unsigned()->index('uniacid');
			$table->string('mobile', 18)->index('mobile');
			$table->string('email', 50)->index('email');
			$table->string('password', 32);
			$table->string('salt', 8);
			$table->integer('groupid')->index('groupid');
			$table->decimal('credit1', 10)->unsigned();
			$table->decimal('credit2', 10)->unsigned();
			$table->decimal('credit3', 10)->unsigned();
			$table->decimal('credit4', 10)->unsigned();
			$table->decimal('credit5', 10)->unsigned();
			$table->decimal('credit6', 10);
			$table->integer('createtime')->unsigned();
			$table->string('realname', 10);
			$table->string('nickname', 20);
			$table->string('avatar');
			$table->string('qq', 15);
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
			$table->string('taobao', 30);
			$table->string('site', 30);
			$table->text('bio', 65535);
			$table->text('interest', 65535);
			$table->string('pay_password', 30);
			$table->boolean('user_from');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_members');
	}

}
