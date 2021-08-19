<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlatformController extends Controller
{
    //
    public function index(){
        global $_W,$_GPC;
        if (empty($_W['isfounder']) && !empty($_W['user']) && ($_W['user']['status'] == 1 || $_W['user']['status'] == 3)) {
            Auth::logout();
            return $this->message('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！');
        }
        if (($_W['setting']['site']['close'] == 1) && empty($_W['isfounder'])) {
            Auth::logout();
            return $this->message('站点已关闭，关闭原因：' . $_W['setting']['site']['closereason'], url('login'), 'error');
        }

        if ($_W['isadmin']) {
            $founder_id = intval($_GPC['founder_id']);
        }

        $account_all_type_sign = AccountService::GetTypeSign();
        $pindex = max(1, intval($_GPC['page']));
        $offset = ($pindex-1)*24;;
        $keyword = trim($_GPC['keyword']);

        $condition = array();
        if (!empty($keyword)) {
            if($keyword=='admin' && $_W['isadmin']){
                $condition[] = ['ISNULL(uni_account_users.uid)', true];
            }else{
                $condition[] = ['uni_account.name','like',"%{$keyword}%"];
            }
        }

        if (!empty($founder_id)) {
            $condition[] = ['uni_account_users.role','vice_founder'];
            $condition[] = ['uni_account_users.uid',$founder_id];
        }

        $query = Account::searchAccountQuery(false)->where($condition);
        $total = $query->count();
        $list = $query->offset($offset)->limit(24)->orderBy('uni_account.createtime','desc')->groupBy('uni_account.uniacid')->get()->keyBy('uniacid')->toArray();

        if (!empty($list)) {
            if (!$_W['isfounder']) {
                $account_user_roles = DB::table('uni_account_users')->where('uid', $_W['uid'])->get()->keyBy('uniacid')->toArray();
            }
            foreach ($list as $k => &$account) {
                $account = uni_fetch($account['uniacid']);
                $account['manageurl'] .= '&iscontroller=0';
                if (!in_array($account_user_roles[$account['uniacid']]['role'], array('owner', 'manager')) && !$_W['isfounder']) {
                    unset($account['manageurl']);
                }
                $account['list_type'] = 'account';
                $account['support_version'] = $account->supportVersion;
                $account['type_name'] = $account->typeName;
                $account['level'] = $account_all_type_sign[$account['type_sign']]['level'][$account['level']];
                $account['user_role'] = $account_user_roles[$account['uniacid']]['role'];
                if ('clerk' == $account['user_role']) {
                    unset($list[$k]);
                    continue;
                }
                $account['is_star'] = DB::table('users_operate_star')->where(array(
                    ['uid',$_W['uid']],
                    ['uniacid',$account['uniacid']],
                    ['module_name','']
                ))->exists() ? 1 : 0;
                if (0 != $account['endtime'] && 2 != $account['endtime'] && $account['endtime'] < TIMESTAMP) {
                    $account['endtime_status'] = 1;
                } else {
                    $account['endtime_status'] = 0;
                }

            }
            if (!empty($list)) {
                $list = array_values($list);
            }
        }

        return $this->globalview('console.platform',array('list'=>$list,'total'=>$total));
    }

    public function checkout(Request $request,$uniacid){
        global $_W;
        $_W['uniacid'] = intval($uniacid);
        $request->session()->put('uniacid',$_W['uniacid']);
        $lastuse = DB::table('users_lastuse')->where(array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid']))->value('modulename');
        if (empty($lastuse)){
            $lastuse = $_W['config']['defaultmodule'];
        }
        return redirect("console/m/{$lastuse}");
    }

    public function account($uniacid){
        global $_W,$_GPC;
    }

    public function utils($uniacid){
        die();
    }

    public function payment(){
    }
}
