<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatVisitIpTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stat_visit_ip', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->bigInteger('ip');
			$table->integer('uniacid');
			$table->string('type', 10);
			$table->string('module', 100);
			$table->integer('date');
			$table->index(['ip','date','module','uniacid'], 'ip_date_module_uniacid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stat_visit_ip');
	}

}
