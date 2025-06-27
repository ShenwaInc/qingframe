<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBalanceAfterToMcCreditsRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('mc_credits_record', 'balance_after')){
            return;
        }
        Schema::table('mc_credits_record', function (Blueprint $table) {
            $table->decimal('balance_after', 10, 2)->unsigned()->nullable()->after('num')->comment('积分变动后的账户余额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mc_credits_record', function (Blueprint $table) {
            $table->dropColumn('balance_after');
        });
    }
}
