<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

define('TIMESTAMP', time());
global $_W,$_GPC;
$_W = $_GPC = array();

class Installed
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
        if(file_exists($installedfile)){
            abort(404);
            die();
        }
        $this->initApp();
        return $next($request);
    }

    public function initApp(){
        global $_W;
        $_W['config'] = config('system');
        $_W['config']['db'] = config('database');
        $_W['timestamp'] = TIMESTAMP;
        $_W['isajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
        View::share('_W',$_W);
    }
}
