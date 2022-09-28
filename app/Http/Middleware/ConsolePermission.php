<?php

namespace App\Http\Middleware;

use App\Services\AccountService;
use App\Services\FileService;
use App\Services\SettingService;
use App\Services\UserService;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

define('IN_SYS', true);
include_once app_path("Helpers/web.php");

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
        SettingService::Load();
        $_W['page'] = $_W['setting']['page'];
        $user = $request->user()->toArray();
        if ($user['endtime']>0 && $user['endtime']<TIMESTAMP){
            Auth::logout();
            session()->save();
            $_W['uid'] = 0;
            $_W['user'] = array('uid'=>0,'username'=>'未登录');
            View::share('_W',$_W);
            echo response()->view('message',array('message'=>'您的账号服务已到期，请联系管理员','redirect'=>'/login','type'=>'error'))->content();
            exit();
        }
        $_W['inconsole'] = true;
        $_W['consolePage'] = url('console');
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
            $_W['account'] = AccountService::FetchUni($uniacid);
            $_W['acid'] = $_W['account']['acid'];
        }
        $_W['attachurl'] = FileService::SetAttachUrl();
        return $next($request);
    }

}
