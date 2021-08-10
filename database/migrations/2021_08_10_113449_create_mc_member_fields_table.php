<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcMemberFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_member_fields', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uniacid')->index('idx_uniacid');
			$table->integer('fieldid')->index('idx_fieldid');
			$table->string('title');
			$table->boolean('available');
			$table->smallInteger('displayorder');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_member_fields');
	}

}
