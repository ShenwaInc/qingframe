<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ConsolePermission
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
        $user = $request->user();
        $_W['uid'] = $user['uid'];
        $_W['user'] = $user->toArray();
        return $next($request);
    }
}
