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
        if (empty($request->user())){
            echo response()->view('message',array('message'=>'请先登录','redirect'=>'/login?redirect=' . urlencode($_W['siteurl']),'type'=>'error'))->content();
            exit();
        }
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
        $_W['inConsole'] = true;
        $_W['consolePage'] = wurl('');
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
        if (!$_W['isfounder']) $this->checkPermission($request, $_W['uid'], $_W['uniacid']);
        return $next($request);
    }

    /**
     * 路由权限判断
     * 目前只判断了是否可以进入应用和服务
     * @param \Illuminate\Http\Request $request  Request
     * @param int  $uniacid 平台id
     * @param int  $uid 当前用户id
     */
    private function checkPermission($request, $uid, $uniacid):void
    {
        global $_W;
        if (empty($uniacid)){
            $uniacid = $request->route('uniacid', $request->input('uniacid'));
        }
        if(empty($uniacid)){
            return;
        }
        $_W['accountRole'] = UserService::AccountRole($uid, $uniacid);
        if (\Str::startsWith($_W['routePath'], 'console/account')){
            if (empty($_W['accountRole'])){
                abort(403, __('没有访问权限'));
            }
        }elseif((\Str::startsWith($_W['routePath'], 'server') || \Str::startsWith($_W['routePath'], 'console/m')) && in_array($_W['accountRole'], ['manager', 'operator'])){
            list($modules, $servers) = UserService::AccountPermission($uniacid, $uid);
            $serverName = $request->route('server');
            if(!empty($serverName) && !isset($servers[$serverName])){
                abort(403, __('没有访问权限'));
            }
            //应用模块判断
            $moduleName = $request->route('modulename');
            if(!empty($moduleName)){
                if(!isset($modules[$moduleName])) abort(403, __('没有访问权限'));
            }
        }
    }

}
