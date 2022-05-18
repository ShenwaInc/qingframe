<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMicroserverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('microserver', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identity', 20);
            $table->string('name', 20);
            $table->string('cover', 255)->default("");
            $table->text("summary")->nullable();
            $table->string("version",10)->default("");
            $table->string("releases",20)->default("");
            $table->string("drive", 10)->default("php");
            $table->string("entrance", 255)->default("");
            $table->mediumtext("datas")->nullable();
            $table->mediumtext("configs")->nullable();
            $table->boolean('status')->default(1);
            $table->integer("addtime")->default(0)->unsigned();
            $table->integer("dateline")->default(0)->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('microserver');
    }
}
