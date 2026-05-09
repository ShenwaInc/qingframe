<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcMappingFansTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('mc_mapping_fans')) return;
        Schema::create('mc_mapping_fans', function(Blueprint $table)
        {
            $table->increments('fanid');
            $table->integer('acid')->unsigned()->index('acid');
            $table->integer('uniacid')->unsigned()->index('uniacid');
            $table->integer('uid')->unsigned()->index('uid');
            $table->string('openid', 50)->unique('openid_2');
            $table->string('nickname', 50)->index('nickname');
            $table->string('groupid', 60);
            $table->char('salt', 8);
            $table->boolean('follow');
            $table->integer('followtime')->unsigned();
            $table->integer('unfollowtime')->unsigned();
            $table->string('tag', 1000);
            $table->integer('updatetime')->unsigned()->nullable()->index('updatetime');
            $table->string('unionid', 64);
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
        Schema::drop('mc_mapping_fans');
    }

}
