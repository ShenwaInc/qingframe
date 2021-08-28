<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\CacheService;
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

    public function checkout($uniacid){
        global $_W;
        $_W['uniacid'] = intval($uniacid);
        session()->put('uniacid',$_W['uniacid']);
        $lastuse = DB::table('users_operate_history')->where(array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid']))->orderBy('createtime','desc')->value('module_name');
        if (empty($lastuse)){
            $lastuse = $_W['config']['defaultmodule'];
        }
        return redirect("console/m/{$lastuse}");
    }

    public function account(Request $request,$action='profile'){
        $method = "account".ucfirst($action);
        if (method_exists($this, $method)){
            return $this->$method($request);
        }
        return $this->message('敬请期待');
    }

    public function accountRemove(Request $request){
        //查询权限
        $uniacid = (int)$request->input('uniacid',0);
        if ($uniacid==0) return $this->message('请选择要删除的平台');
        global $_W;
        $role = UserService::AccountRole($_W['uid'],$uniacid);
        if (!in_array($role,array('founder','owner')) && !$_W['isfounder']){
            return $this->message('暂无权限，请勿乱操作');
        }
        //删除平台
        DB::table('account')->where('uniacid',$uniacid)->update(array('isdeleted'=>1));
        DB::table('uni_modules')->where('uniacid',$uniacid)->delete();
        DB::table('users_operate_star')->where('uniacid',$uniacid)->delete();
        DB::table('users_lastuse')->where('uniacid',$uniacid)->delete();
        DB::table('core_menu_shortcut')->where('uniacid',$uniacid)->delete();
        DB::table('uni_link_uniacid')->where('uniacid',$uniacid)->delete();
        $cachekey = CacheService::system_key('user_accounts', array('type' => 'account', 'uid' => $_W['uid']));
        Cache::forget($cachekey);
        $cachekey = CacheService::system_key('uniaccount', array('uniacid' => $uniacid));
        Cache::forget($cachekey);
        return $this->message('删除成功！',url('console'),'success');
    }

    public function accountCreate(Request $request){
        if ($request->isMethod('post')){
            global $_W;
            $post = $request->input('data');
            if (empty($post['name'])) return $this->message('平台名称不能为空');
            if (empty($post['logo'])) return $this->message('请上传平台LOGO');
            $uni_account = DB::table('uni_account');
            $uniacid = $uni_account->insertGetId(array(
                'groupid' => 0,
                'default_acid' => 0,
                'name' => $post['name'],
                'description' => $post['description'],
                'logo'=>$post['logo'],
                'title_initial' => 'W',
                'createtime' => TIMESTAMP,
                'create_uid' => $_W['uid']
            ));
            if (!empty($uniacid)){
                $acid = Account::account_create($uniacid,array('name'=>$post['name']));
                $uni_account->where('uniacid',$uniacid)->update(array('default_acid' => $acid));
                UserService::AccountRoleUpdate($uniacid,$_W['uid']);

                DB::table('mc_groups')->insert(array('uniacid' => $uniacid, 'title' => '默认会员组', 'isdefault' => 1));
                DB::table('uni_settings')->insert(array(
                    'creditnames' => serialize(array('credit1' => array('title' => '积分', 'enabled' => 1), 'credit2' => array('title' => '余额', 'enabled' => 1))),
                    'creditbehaviors' => serialize(array('activity' => 'credit1', 'currency' => 'credit2')),
                    'uniacid' => $uniacid,
                    'default_site' => 0,
                    'sync' => serialize(array('switch' => 0, 'acid' => '')),
                ));

                return $this->message('恭喜您，创建成功！',url('console/account',array('uniacid'=>$uniacid)),'success');
            }
            return $this->message('创建失败，请重试');
        }
        $return = array('title'=>'创建平台');
        return $this->globalview('console.account.create', $return);
    }

}
