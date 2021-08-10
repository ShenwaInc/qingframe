<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWxappGeneralAnalysisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wxapp_general_analysis', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->index('uniacid');
			$table->integer('session_cnt');
			$table->integer('visit_pv');
			$table->integer('visit_uv');
			$table->integer('visit_uv_new');
			$table->boolean('type');
			$table->string('stay_time_uv', 10);
			$table->string('stay_time_session', 10);
			$table->string('visit_depth', 10);
			$table->string('ref_date', 8)->index('ref_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wxapp_general_analysis');
	}

}
