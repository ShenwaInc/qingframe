<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfileFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_fields', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('field');
			$table->boolean('available');
			$table->string('title');
			$table->string('description');
			$table->smallInteger('displayorder');
			$table->boolean('required');
			$table->boolean('unchangeable');
			$table->boolean('showinregister');
			$table->integer('field_length');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profile_fields');
	}

}
