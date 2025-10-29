<?php

namespace App\Http\Middleware;

use App\Models\SystemLog;
use Closure;
use Illuminate\Support\Facades\Schema;

class Installer
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $installed = Schema::hasTable("account");
        }catch (\Exception $exception){
            if(in_array($exception->getCode(), [1044, 1045, 2002])){
                $installed = false;
            }else{
                SystemLog::systemRunning(
                    '数据库表检查异常',
                    'middleware:Installer',
                    "中间件检查系统安装状态时发生异常：{$exception->getMessage()}",
                    false,
                    [
                        'exception_file' => $exception->getFile(),
                        'exception_line' => $exception->getLine(),
                        'exception_code' => $exception->getCode(),
                        'exception_trace' => $exception->getTrace(),
                    ]
                );
                throw $exception;
            }
        }

        if(!$installed){
            //系统未安装
            header('Location: ' . url('installer'));
            exit();
        }
        return $next($request);
    }

}
