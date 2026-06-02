<?php

namespace App\Providers;

use App\Jobs\RecordSlowQuery;
use App\Models\SystemLogs;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //定义微服务模板调用
        Blade::directive("serverView", function ($template=''){
            $realname = str_replace(array(":", ".","'", '"'), array("/views/", "/"), $template);
            $path = base_path("servers/" . $realname . ".blade.php");
            if (!file_exists($path)){
                $platform = defined("IN_SYS") ? "web" : "app";
                $path = str_replace("/views/", "/views/$platform/", $path);
                if (!file_exists($path)){
                    throw new \Exception("View [$template] not found.(Path: $path)");
                }
            }
            return "<?php echo Illuminate\Support\Facades\View::file('$path')->render() ?>";
        });
        //定义应用模块模板调用
        Blade::directive("moduleView", function ($template=''){
            if (strexists($template, ":")){
                $view = str_replace(array(":", ".","'", '"'), array("/views/", "/"), $template);
            }else{
                global $_MODULE_VIEW;
                $view = "$_MODULE_VIEW/views/$template";
            }
            $path = public_path("addons/" . $view . ".blade.php");
            if (!file_exists($path)){
                $platform = defined("IN_SYS") ? "web" : "app";
                $path = str_replace("/views/", "/views/$platform/", $path);
                if (!file_exists($path)){
                    throw new \Exception("View [$template] not found.(Path: $path)");
                }
            }
            return "<?php echo Illuminate\Support\Facades\View::file('$path')->render() ?>";
        });
        //异步记录慢查询日志
        DB::listen(function ($query) {
            // 忽略日志、队列相关操作
            if (strpos($query->sql, 'system_logs') !== false || strpos($query->sql, 'failed_jobs') !== false){
                return;
            }
            // 慢查询阈值（单位：秒，示例为1秒）
            $slowThreshold = config('system.log.slow_query_threshold', 0);
            if ($slowThreshold && $query->time > $slowThreshold){
                //记录日志查询
                $fullSql = SystemLogs::formatSql($query->sql, $query->bindings);
                $pageUrl = request()->fullUrl();
                RecordSlowQuery::dispatch(
                    $fullSql,
                    $query->time, // 执行时间（毫秒）
                    $query->connectionName, // 数据库连接名
                    $query->bindings, // 绑定参数
                    $pageUrl
                );
            }
        });
    }

}
