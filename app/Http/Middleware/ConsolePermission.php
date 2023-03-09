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
        //路由权限判断
        if (!$_W['isfounder']) $this->checkPermission($request,$_W['uid'],$_W['uniacid']);
        return $next($request);
    }

    /**
     * 路由权限判断（待完善）
     * 目前只判断了是否可以进入应用和服务
     * @param object $request  Request
     * @param int    $uniacid 平台id
     * @param int    $uid 当前用户id
     */
    private function checkPermission($request,int $uid,int $uniacid){
        $permission= DB::table('users_permission')->where(['uid'=>$uid,'uniacid'=>$uniacid])->value('permission');
        $permission=unserialize($permission);
        //微服务权限判断
        $serverName=$request->route('server');
        if(!empty($serverName) && empty($permission['servers'][$serverName])){
            message('没有访问权限');
        }
        //应用模块判断
        $modulename=$request->route('modulename');
        if(!empty($modulename) && empty($permission['modules'][$modulename])){
            message('没有访问权限');
        }
    }

}
