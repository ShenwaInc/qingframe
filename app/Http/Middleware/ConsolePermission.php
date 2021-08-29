<?php

namespace App\Http\Middleware;

use App\Services\AttachmentService;
use App\Services\UserService;
use Closure;
use Illuminate\Support\Facades\DB;

define('IN_SYS', true);

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
        $_W['inconsole'] = true;
        $user = $request->user()->toArray();
        $_W['uid'] = $user['uid'];
        $profile = DB::table('users_profile')->where('uid',$_W['uid'])->select('avatar','gender','mobile','email')->first();
        $_W['user'] = array_merge($user,$profile);
        $_W['username'] = $_W['user']['username'];
        $_W['isfounder'] = UserService::isFounder($_W['uid']);
        $_W['isadmin'] = UserService::isFounder($_W['uid'],true);
        $_W['highest_role'] = UserService::AccountRole($_W['uid']);
        $_W['role'] = '';
        $uniacid = (int)session('uniacid',0);
        if ($uniacid>0){
            $_W['uniacid'] = $uniacid;
            $_W['role'] = UserService::AccountRole($_W['uid'],$uniacid);
            $_W['account'] = uni_fetch($uniacid);
        }
        $_W['attachurl'] = AttachmentService::SetAttachUrl();
        include_once base_path("bootstrap/functions/console.func.php");
        return $next($request);
    }

}
