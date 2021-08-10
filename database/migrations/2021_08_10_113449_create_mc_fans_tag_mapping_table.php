<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcFansTagMappingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_fans_tag_mapping', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('fanid')->unsigned()->index('fanid_index');
			$table->string('tagid', 20)->index('tagid_index');
			$table->unique(['fanid','tagid'], 'mapping');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mc_fans_tag_mapping');
	}

}
