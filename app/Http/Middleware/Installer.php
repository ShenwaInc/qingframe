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
            header('Location: ' . url('installer'));
            exit();
        }
        return $next($request);
    }

}
