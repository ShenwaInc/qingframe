<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\CacheService;
use App\Services\ModuleService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PlatformController extends Controller
{
    //
    public function index(){
        global $_W,$_GPC;

        session()->forget('uniacid');
        if (empty($_W['isfounder']) && !empty($_W['user']) && ($_W['user']['status'] == 1 || $_W['user']['status'] == 3)) {
            Auth::logout();
            return $this->message('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！');
        }
        if (($_W['setting']['site']['close'] == 1) && empty($_W['isfounder'])) {
            Auth::logout();
            return $this->message('站点已关闭，关闭原因：' . $_W['setting']['site']['closereason'], url('login'), 'error');
        }

        $data = array('cancreate'=>true);

        $params = post_var(array('keyword'));

        if ($_W['isadmin']) {
            $params['founder_id'] = intval($_GPC['founder_id']);
        }

        list($data['list'], $data['total']) = AccountService::OwnerAccounts($params, $_GPC['page']);

        if (!$_W['isfounder']){
            $maxCreate = (int)DB::table('users_extra_limit')->where('uid',$_W['uid'])->value('maxaccount');
            if ($maxCreate<=$data['total']){
                $data['cancreate'] = false;
            }
        }

        return $this->globalview('console.platform', $data);
    }

    public function checkout(Request $request,$uniacid){
        global $_W;
        $_W['uniacid'] = intval($uniacid);
        session()->put('uniacid',$_W['uniacid']);
        $module = $request->input('module');
        if (empty($module)){
            $module = DB::table('users_operate_history')->where(array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid']))->orderBy('createtime','desc')->value('module_name');
            if (empty($module)){
                $module = $_W['config']['defaultmodule'];
            }
        }
        $moduleObj = ModuleService::fetch($module);
        if (empty($moduleObj)){
            return redirect("console/account/profile?uniacid={$_W['uniacid']}");
        }
        return redirect("console/m/$module");
    }

}
