<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersExtraTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_extra_templates', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('uid')->index('uid');
			$table->integer('template_id')->index('template_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_extra_templates');
	}

}
