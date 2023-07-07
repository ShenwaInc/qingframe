<?php

namespace App\Services;

use App\Models\Account;
use App\Utils\WeAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccountService {

    static function GetType($type = 0){
        $all_account_type = array(
            1=>array(
                'title' => '公众号',
                'type_sign' => 'account',
                'table_name' => 'account_wechats',
                'module_support_name' => 'account_support',
                'module_support_value' => 2,
                'store_type_module' => 1,
                'store_type_number' => 2,
                'store_type_renew' => 7
            )
        );
        if (!empty($type)) {
            return !empty($all_account_type[$type]) ? $all_account_type[$type] : array();
        }
        return $all_account_type;
    }

    static function GetTypeSign($type_sign = ''){
        $all_account_type_sign = array(
            'account' => array(
                'contain_type' => array(1, 3),
                'level' => array(1 => '订阅号', 2 => '服务号', 3 => '认证订阅号', 4 => '认证服务号'),
                'icon' => 'wi wi-wx-circle',
                'createurl' => url('account/post'),
                'title' => '公众号'
            )
        );
        if (!empty($type_sign)) {
            return !empty($all_account_type_sign[$type_sign]) ? $all_account_type_sign[$type_sign] : array();
        }
        return $all_account_type_sign;
    }

    static function GetOprateStar($uid,$uniacid,$module_name=''){
        $condition = array('uid'=>$uid, 'uniacid'=>$uniacid);
        if (!empty($module_name)){
            $condition['module_name'] = $module_name;
        }
        return DB::table('users_operate_star')->where($condition)->first();
    }

    static function GetEntrance($uid,$uniacid){
        $entrance = pdo_getcolumn('uni_account_users', array('uid'=>$uid,'uniacid'=>$uniacid), 'entrance');
        if (empty($entrance)){
            $entrance = 'account:profile';
        }
        return explode(":", $entrance);
    }

    static function GetAllEntrances($uniacid, $uid=0){
        $entrances = array(
            'account'=>array(
                'profile'=>"基础信息",
                'functions'=>'应用与服务',
                'role'=>'操作权限'
            ),
            'module'=>[],
            'server'=>[]
        );
        $role = UserService::AccountRole($uid, $uniacid);
        if (!in_array($role, array('founder', 'owner'))){
            unset($entrances['account']['role']);
        }
        $modules = self::ExtraModules($uniacid);
        if (!empty($modules)){
            foreach ($modules as $module){
                $entrances['module'][$module['identity']] = $module['name'];
            }
        }
        $servers = DB::table('microserver_unilink')->where('status', 1)->get(['name','title','entry'])->toArray();
        if (!empty($servers)){
            foreach ($servers as $server){
                $entrances['server'][$server['name']] = $server['title'];
            }
        }
        return $entrances;
    }

    static function FetchUni($uniacid = 0) {
        global $_W;
        $uniacid = empty($uniacid) ? $_W['uniacid'] : intval($uniacid);
        $account_api = WeAccount::createByUniacid($uniacid);
        if (is_error($account_api)) {
            return $account_api;
        }
        $account_api->uniacid = $uniacid;
        $account_api->__toArray();
        $account_api['accessurl'] = $account_api['manageurl'] = url("console/account/$uniacid/post", array('account_type' => $account_api['type']), true);
        $account_api['roleurl'] = url("console/account/$uniacid/postuser", array('account_type' => $account_api['type']), true);
        return $account_api;
    }

    static function Create_info() {
        global $_W;
        $account_create_info = PermissionService::UserAccountNum();
        $can_create = false;
        if ($_W['isadmin'] || (!empty($account_create_info['account_limit']) && (!empty($account_create_info['founder_account_limit']) && $_W['user']['owner_uid'] || empty($_W['user']['owner_uid'])) || !empty($account_create_info['store_account_limit']))){
            $can_create = true;
        }
        $all_account_type_sign = self::GetTypeSign();
        $all_account_type_sign['account']['can_create'] = $can_create;
        return $all_account_type_sign;
    }

    static function GroupModules($uniacid){
        $packageids = DB::table('uni_account_group')->where('uniacid', $uniacid)->select(['groupid'])->get()->keyBy('groupid')->toArray();
        $packageids = empty($packageids) ? array() : array_keys($packageids);
        if (in_array('-1', $packageids)) {
            $modules = DB::table('modules')->select(['name'])->get()->keyBy('name')->toArray();
            return array_keys($modules);
        }
        $uni_modules = array();

        $uni_groups = DB::table('uni_group')->where('id', $packageids)->get()->toArray();
        $uni_account_extra_modules = DB::table('uni_account_extra_modules')->where('uniacid', $uniacid)->get()->toArray();
        $acount_modules = array_merge($uni_groups, $uni_account_extra_modules);
        if (!empty($acount_modules)) {
            $uni_modules = array_unique($uni_modules);
        }
        return $uni_modules;
    }

    static function ExtraModules($uniacid){
        $cachekey = CacheService::system_key("unimodules", array('uniacid'=>$uniacid));
        $modules = Cache::get($cachekey, error(-1, 'nothing'));
        if (is_error($modules)){
            $modules = [];
            $_modules = DB::table('uni_account_extra_modules')->where('uniacid', $uniacid)->value('modules');
            $extra_modules = $_modules ? unserialize($_modules) : [];
            if (!empty($extra_modules)){
                foreach ($extra_modules as $val){
                    $val['logo'] = asset($val['logo']);
                    $modules[$val['identity']] = $val;
                }
            }
            Cache::put($cachekey, $modules, 7*86400);
            return $modules;
        }
        return $modules;
    }

    static function OwnerAccountNums($uid, $role){
        $account_all_type = self::GetType();
        $account_all_type_sign = array('account');

        $num = array('account_num'=>0);

        foreach ($account_all_type_sign as $type_info) {
            $key_name = $type_info . 'account_num';
            $num[$key_name] = 0;
        }

        $uniacocunts = Account::searchAccountList();

        if (!empty($uniacocunts)) {
            $uni_account_users_table = DB::table('uni_account_users')->join('account','uni_account_users.uniacid','=','account.uniacid');
            $all_account = $uni_account_users_table->where(array(
                ['uni_account_users.role',$role],
                ['uni_account_users.uid',$uid]
            ))->get()->keyBy('uniacid')->toArray();

            foreach ($all_account as $account) {
                foreach ($account_all_type as $type_key => $type_info) {
                    if ($type_key == $account['type']) {
                        $key_name = $type_info['type_sign'] . '_num';
                        $num[$key_name] += 1;
                        continue;
                    }
                }
            }
        }

        return $num;
    }

    static function OwnerAccounts($params, $page=1, $getlist=false){
        global $_W;
        if (!empty($params['founder_id'])) {
            $founder_id = intval($params['founder_id']);
        }

        $account_all_type_sign = self::GetTypeSign();
        $pIndex = max(1, intval($page));
        $pSize = empty($params['pagesize']) ? 24 : min(1, intval($params['pagesize']));
        $offset = ($pIndex-1)*$pSize;
        $keyword = trim($params['keyword']);

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
        $created = 0;
        if ($page!=-1){
            $query = $query->offset($offset)->limit($pSize);
        }
        $list = $query->orderBy('uni_account.createtime','desc')->groupBy('uni_account.uniacid')->get()->keyBy('uniacid')->toArray();

        if (!empty($list)) {
            if (!$_W['isfounder']) {
                $account_user_roles = DB::table('uni_account_users')->where('uid', $_W['uid'])->get()->keyBy('uniacid')->toArray();
            }
            foreach ($list as $k => &$account) {
                $account = AccountService::FetchUni($account['uniacid']);
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
                if ($account['user_role']=='owner' || $account['user_role']=='founder'){
                    $created += 1;
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

        if ($getlist) return $list ?: [];

        return array($list, $total, $created);
    }

    static function OauthHost(){
        global $_W;
        $oauth_url = $_W['siteroot'];
        $unisetting = SettingService::uni_load();
        if (!empty($unisetting['bind_domain']) && !empty($unisetting['bind_domain']['domain'])) {
            $oauth_url = $unisetting['bind_domain']['domain'] . '/';
        } else {
            if (1 == $_W['account']['type']) {
                if (!empty($unisetting['oauth']['host'])) {
                    $oauth_url = $unisetting['oauth']['host'] . '/';
                } else {
                    $global_unisetting = self::GlobalOauth();
                    $oauth_url = !empty($global_unisetting['oauth']['host']) ? $global_unisetting['oauth']['host'] . '/' : $oauth_url;
                }
            }
        }
        return $oauth_url;
    }

    static function GlobalOauth(){
        $oauth = SettingService::Load('global_oauth');
        $oauth = !empty($oauth['global_oauth']) ? $oauth['global_oauth'] : array();
        if (!empty($oauth['oauth']['account'])) {
            $account_exist = self::FetchUni($oauth['oauth']['account']);
            if (empty($account_exist) || is_error($account_exist)) {
                $oauth['oauth']['account'] = 0;
            }
        }
        return $oauth;
    }

}
