<?php

namespace App\Http\Middleware;

use Closure;

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
        $installedfile = base_path('storage/installed.bin');
        if(!file_exists($installedfile)){
            //系统未安装
            $installroute = url('installer');
            header('Location: ' . $installroute);
            exit();
        }elseif(env('APP_DEVELOPMENT',0)==0){
            $installer = app_path('Http/Controllers/InstallController.php');
            if (file_exists($installer)){
                @unlink($installer);
            }
        }
        return $next($request);
    }

}
