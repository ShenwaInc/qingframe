<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUniSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('uni_settings', function(Blueprint $table)
		{
			$table->integer('uniacid')->unsigned()->primary();
			$table->string('passport', 200);
			$table->string('oauth', 100);
			$table->integer('jsauth_acid')->unsigned();
			$table->string('notify', 2000);
			$table->string('creditnames', 500);
			$table->string('creditbehaviors', 500);
			$table->string('welcome', 60);
			$table->string('default', 60);
			$table->string('default_message', 2000);
			$table->text('payment');
            $table->text('notice');
			$table->string('stat', 300);
			$table->integer('default_site')->unsigned()->nullable();
			$table->boolean('sync');
			$table->string('recharge', 500);
			$table->string('tplnotice', 2000);
			$table->boolean('grouplevel');
			$table->string('mcplugin', 500);
			$table->boolean('exchange_enable');
			$table->boolean('coupon_type');
			$table->text('menuset');
			$table->string('statistics', 100);
			$table->string('bind_domain', 200);
			$table->boolean('comment_status');
			$table->boolean('reply_setting');
			$table->string('default_module', 100);
			$table->integer('attachment_limit');
			$table->string('attachment_size', 20);
			$table->boolean('sync_member');
			$table->string('remote', 2000);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('uni_settings');
	}

}
