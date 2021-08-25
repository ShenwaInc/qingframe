<?php

namespace App\Http\Middleware;

use Closure;

define('IN_MOBILE', true);

class AppRuntime
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
        global $_W;
        $uniacid = $request->input('i',0);
        if (empty($uniacid)) abort(404,'找不到该平台');
        $_W['uniacid'] = $uniacid;
        return $next($request);
    }
}
