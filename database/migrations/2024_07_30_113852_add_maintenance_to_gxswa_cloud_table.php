<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaintenanceToGxswaCloudTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('gxswa_cloud', 'maintenance')){
            Schema::table('gxswa_cloud', function (Blueprint $table) {
                $table->boolean('maintenance')->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('gxswa_cloud', 'maintenance')){
            Schema::table('gxswa_cloud', function (Blueprint $table) {
                $table->dropColumn(['maintenance']);
            });
        }
    }
}
