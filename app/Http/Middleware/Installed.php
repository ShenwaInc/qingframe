<?php

namespace App\Http\Middleware;

use Closure;

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
        return $next($request);
    }
}
