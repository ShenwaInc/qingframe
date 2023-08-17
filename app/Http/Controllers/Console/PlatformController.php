<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\ModuleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlatformController extends Controller
{
    //
    public function index(){
        global $_W,$_GPC;

        if (empty($_W['isfounder']) && !empty($_W['user']) && ($_W['user']['status'] == 1 || $_W['user']['status'] == 3)) {
            Auth::logout();
            return $this->message('accountUnavailable');
        }
        if (($_W['setting']['site']['close'] == 1) && empty($_W['isfounder'])) {
            Auth::logout();
            return $this->message(__('closingFor', array('reason'=>$_W['setting']['site']['closereason'])), url('login'), 'error');
        }

        if (SITEACID){
            return redirect("console/account/".SITEACID);
        }

        session()->forget('uniacid');
        $data = array('cancreate'=>true);
        $params = post_var(array('keyword'));

        if ($_W['isadmin']) {
            $params['founder_id'] = intval($_GPC['founder_id']);
        }

        list($data['list'], $data['total'], $data['created']) = AccountService::OwnerAccounts($params, $_GPC['page']);

        if (!$_W['isfounder']){
            $maxCreate = (int)DB::table('users_extra_limit')->where('uid',$_W['uid'])->value('maxaccount');
            if ($maxCreate<=$data['created']){
                $data['cancreate'] = false;
            }
        }

        return $this->globalView('console.platform', $data);
    }

    public function checkout($uniacid){
        global $_W;
        if ($_W['config']['site']['id']==0){
            return redirect("console/active");
        }
        $_W['uniacid'] = intval($uniacid);
        session()->put('uniacid',$_W['uniacid']);
        list($controller, $method) = AccountService::GetEntrance($_W['uid'], $_W['uniacid']);
        if ($controller=='module'){
            $module_exists = ModuleService::fetch($method);
            if (empty($module_exists) || is_error($module_exists)){
                $controller = 'account';
                $method = "profile";
            }else{
                return redirect("console/m/$method");
            }
        }
        if ($controller=='account'){
            return redirect("console/account/{$method}?uniacid={$_W['uniacid']}");
        }else{
            $redirect = serv($method)->getEntry();
            return redirect($redirect);
        }
    }

}
