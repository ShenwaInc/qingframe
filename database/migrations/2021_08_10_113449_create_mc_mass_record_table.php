<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcMassRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_mass_record', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->unsigned();
			$table->integer('acid')->unsigned();
			$table->string('groupname', 50);
			$table->integer('fansnum')->unsigned();
			$table->string('msgtype', 10);
			$table->string('content', 10000);
			$table->integer('group');
			$table->integer('attach_id')->unsigned();
			$table->string('media_id', 100);
			$table->boolean('type');
			$table->boolean('status');
			$table->integer('cron_id')->unsigned();
			$table->integer('sendtime')->unsigned();
			$table->integer('finalsendtime')->unsigned();
			$table->integer('createtime')->unsigned();
			$table->string('msg_id', 50);
			$table->string('msg_data_id', 50);
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
		Schema::drop('mc_mass_record');
	}

}
