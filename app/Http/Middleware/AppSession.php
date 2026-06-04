<?php

namespace App\Http\Middleware;

use Closure;

class AppSession
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
        if ($sessionId = $request->header('x-session-id', '')) {
            // 直接操作底层 Session 管理器，避免提前启动
            app('session')->setId($sessionId);
            app('session')->start();
        }
        return $next($request);
    }
}
