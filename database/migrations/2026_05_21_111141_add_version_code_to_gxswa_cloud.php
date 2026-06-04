<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVersionCodeToGxswaCloud extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('gxswa_cloud', 'version_code')){
            Schema::table('gxswa_cloud', function (Blueprint $table) {
                $table->bigInteger('version_code')->nullable(false)->default('0')->comment('版本号')->after('version');
            });

            if (Schema::hasColumn('gxswa_cloud', 'releasedate')){
                $modules = DB::table('gxswa_cloud')->get();
                foreach ($modules as $module) {
                    DB::table('gxswa_cloud')->where('id', $module->id)->update(['version_code' => $module->releasedate]);
                }

                Schema::table('gxswa_cloud', function (Blueprint $table) {
                    $table->dropColumn('releasedate');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('gxswa_cloud', 'version_code')){
            Schema::table('gxswa_cloud', function (Blueprint $table) {
                $table->dropColumn('version_code');
            });
        }
    }
}
