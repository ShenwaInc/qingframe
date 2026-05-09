<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSystemLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('system_logs')){
            Schema::create('system_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('type', 20)->comment('日志类型');
                $table->string('module', 50)->comment('操作模块');
                $table->string('title')->comment('日志标题');
                $table->text('content')->nullable()->comment('日志详情');
                $table->unsignedInteger('user_id')->nullable()->comment('操作人ID');
                $table->string('username', 50)->nullable()->comment('操作人用户名');
                $table->string('ip', 45)->nullable()->comment('操作IP地址');
                $table->text('url')->nullable()->comment('请求URL');
                $table->string('method', 10)->nullable()->comment('请求方法');
                $table->boolean('status')->default(1)->comment('状态：1=成功，0=失败');
                $table->string('error_code', 50)->nullable()->comment('错误码');
                $table->unsignedInteger('cost_ms')->nullable()->comment('耗时(ms)');
                $table->text('extra')->nullable()->comment('额外信息');
                $table->timestamps();
                $table->index('type');
                $table->index('user_id');
                $table->index('status');
                $table->index('created_at');
            });
        }

        // 给表添加注释（MySQL支持）
        if(Schema::hasTable('system_logs')){
            DB::statement("ALTER TABLE " . tablename('system_logs') . " COMMENT '系统日志表'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_logs');
    }
}
