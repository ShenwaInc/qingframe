<?php

namespace App\Http\Middleware;

use App\Services\AccountService;
use App\Services\ModuleService;
use App\Services\UserService;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class ModulePermission
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
        //查询平台权限
        if (empty($_W['uniacid'])){
            //最后使用
            $uniacid = (int)DB::table('users_operate_history')->where('uid',$_W['uid'])->orderBy('createtime','desc')->value('uniacid');
            //默认平台权限
            if ($uniacid==0){
                //$modulename = $request->route('modulename');
                $uniacid = (int)DB::table('uni_account_users')->where(array('uid'=>$_W['uid']))->whereIn('role',array('owner','manager','opreator'))->orderBy('id','desc')->value('uniacid');
            }
            if ($uniacid==0){
                $this->message("暂无可用平台");
            }
            $_W['uniacid'] = $uniacid;
            $_W['role'] = UserService::AccountRole($_W['uid'],$uniacid);
            $_W['account'] = AccountService::FetchUni($uniacid);
            session()->put('uniacid',$uniacid);
        }
        //查询应用权限
        $module = ModuleService::fetch($request->segment(3));
        if (empty($module)){
            $this->message("找不到该应用");
        }
        if (!$_W['isfounder']){
            $role = UserService::AccountRole($_W['uid'], $_W['uniacid']);
            switch ($role){
                case "owner":
                case "manager" : {
                    $ExtraModules = AccountService::ExtraModules($_W['uniacid']);
                    if (!isset($ExtraModules[$module['name']])){
                        $this->message("暂无权限");
                    }
                    break;
                }
                case "operator" :{

                    break;
                }
            }
        }
        return $next($request);
    }

    public function message($msg){
        global $_W;
        View::share('_W',$_W);
        echo response()->view('message',array('message'=>$msg,'type'=>'error','redirect'=>wurl('')))->content();
        session_exit();
    }
}
