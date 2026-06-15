<?php

namespace App\Services;

use App\Models\UniAccount;
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
                'createurl' => wurl('account/create'),
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

    static function remoteAccount($uniacid)
    {
        //删除平台
        DB::table('account')->where('uniacid',$uniacid)->update(array('isdeleted'=>1));
        DB::table('uni_modules')->where('uniacid',$uniacid)->delete();
        DB::table('users_operate_star')->where('uniacid',$uniacid)->delete();
        DB::table('users_operate_history')->where('uniacid', $uniacid)->delete();
        $cacheKey = CacheService::system_key('user_accounts', array('type' => 'account', 'uid' => $_W['uid']));
        Cache::forget($cacheKey);
        $cacheKey = CacheService::system_key('uniaccount', array('uniacid' => $uniacid));
        Cache::forget($cacheKey);
    }

    static function createAccount($data, $uid=0)
    {
        if (empty($data['name'])){
            return error(-1, __('platformNameEmpty'));
        }
        if (empty($data['logo'])){
            $data['logo'] = '/static/icon200.jpg';
        }
        $uni_account = DB::table('uni_account');
        $accountData = array(
            'groupid' => 0,
            'default_acid' => 0,
            'name' => $data['name'],
            'description' => trim($data['description']),
            'logo'=>$data['logo'],
            'title_initial' => 'W',
            'createtime' => TIMESTAMP,
            'create_uid' => $uid
        );
        $uniacid = $uni_account->insertGetId($accountData);
        if (empty($uniacid)){
            return error(-1, __('saveFailed'));
        }
        $accountData['uniacid'] = $uniacid;
        $acid = UniAccount::account_create($uniacid,array('name'=>$accountData['name']));
        $uni_account->where('uniacid',$uniacid)->update(array('default_acid' => $acid));
        UserService::AccountRoleUpdate($uniacid, $uid);
        return $accountData;
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
        $account_api['accessurl'] = $account_api['manageurl'] = wurl("account/$uniacid/post", array('account_type' => $account_api['type']), true);
        $account_api['roleurl'] = wurl("account/$uniacid/postuser", array('account_type' => $account_api['type']), true);
        return $account_api;
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

    static function UpdateModules($uniacid, $name, $data=[]){
        $modules = self::ExtraModules($uniacid, false);
        $module = $modules[$name];
        if (!empty($module)){
            $modify = false;
            unset($data['identity']);
            foreach ($module as $key=>$value){
                if (!empty($data[$key])){
                    $module[$key] = $data[$key];
                    if ($data[$key]!=$value) $modify = true;
                }
            }
            if (!$modify) return true;
        }else{
            if (empty($data['name']) || empty($data['logo'])){
                return false;
            }
            $module = $data;
            $module['identity'] = $name;
        }
        $module['profile'] = 'custom';
        $modules[$name] = $module;
        if (DB::table('uni_account_extra_modules')->updateOrInsert(array('uniacid'=>$uniacid), array('modules'=>serialize($modules)))){
            CacheService::flush();
            return true;
        }
        return false;
    }

    static function ExtraModules($uniacid, $cache=true){
        $cacheKey = CacheService::system_key("unimodules", array('uniacid'=>$uniacid));
        $modules = [];
        $default = error(-1, 'nothing');
        if ($cache){
            $modules = Cache::get($cacheKey, []);
            if (!empty($modules)){
                return is_error($modules) ? [] : $modules;
            }
        }
        $_modules = DB::table('uni_account_extra_modules')->where('uniacid', $uniacid)->value('modules');
        $extra_modules = $_modules ? unserialize($_modules) : [];
        if (!empty($extra_modules)){
            foreach ($extra_modules as $val){
                $modules[$val['identity']] = $val;
            }
        }
        Cache::put($cacheKey, empty($modules)?$default:$modules, 7*86400);
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

        $uniacocunts = UniAccount::searchAccountList();

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

        $query = UniAccount::searchAccountQuery(false)->where($condition);
        $total = $query->count();
        $created = 0;
        if ($page!=-1){
            $query = $query->offset($offset)->limit($pSize);
        }
        $list = $query->orderBy('uni_account.createtime','desc')->groupBy('uni_account.uniacid')->get()->keyBy('uniacid')->toArray();
        $account_user_roles = [];

        if (!empty($list)) {
            if (!$_W['isfounder']) {
                $account_user_roles = DB::table('uni_account_users')->where('uid', $_W['uid'])->get()->keyBy('uniacid')->toArray();
            }
            foreach ($list as $k => $account) {
                $account = self::parseAccount($account, $account_user_roles, $account_all_type_sign);
                if ($account['user_role']=='owner' || $account['user_role']=='founder'){
                    $created += 1;
                }
                if (empty($account)){
                    unset($list[$k]);
                    continue;
                }
                $list[$k] = $account;
            }
            if (!empty($list)) {
                $list = array_values($list);
            }
        }

        if ($getlist) return $list ?: [];

        return array($list, $total, $created);
    }

    static function parseAccount($account, $account_user_roles=[], $account_all_type_sign=[])
    {
        global $_W;
        $account = AccountService::FetchUni($account['uniacid']);
        $account['manageUrl'] .= '&iscontroller=0';
        if (!in_array($account_user_roles[$account['uniacid']]['role'], array('owner', 'manager')) && !$_W['isfounder']) {
            unset($account['manageUrl']);
        }
        $account['list_type'] = 'account';
        $account['support_version'] = $account->supportVersion;
        $account['type_name'] = $account->typeName;
        $account['level'] = $account_all_type_sign[$account['type_sign']]['level'][$account['level']];
        $account['user_role'] = $account_user_roles[$account['uniacid']]['role'];
        if ('clerk' == $account['user_role']) {
            return [];
        }
        $account['is_star'] = DB::table('users_operate_star')->where(array(
            ['uid', $_W['uid']],
            ['uniacid',$account['uniacid']],
            ['module_name','']
        ))->exists() ? 1 : 0;
        if (0 != $account['endtime'] && 2 != $account['endtime'] && $account['endtime'] < TIMESTAMP) {
            $account['endtime_status'] = 1;
        } else {
            $account['endtime_status'] = 0;
        }
        return $account;
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

    static function moduleAuthenticate($uid, $name, $route, $uniacid=0, $isServer=false){
        global $_W;
        if ($_W['isfounder']) return true;
        if (empty($uniacid)){
            if (empty($_W['uniacid']))
            $uniacid = $_W['uniacid'];
        }
        list($modules, $servers, $role) = UserService::AccountPermission($uniacid, $uid);
        if (empty($role)){
            abort(403, __('没有访问权限'));
        }
        $hasPerm = $isServer ? isset($servers[$name]) : isset($modules[$name]);
        if (!$hasPerm){
            abort(403, __('没有访问权限'));
        }
        if (!empty($route)){
            $permission = $isServer ? ($servers[$name]?:[]) : ($modules[$name]?:[]);
            if (!in_array($route, $permission)){
                abort(403, __('没有访问权限'));
            }
        }
        return true;
    }

    static function serverAuthenticate($uid, $name, $route, $uniacid=0){
        return self::moduleAuthenticate($uid, $name, $route, $uniacid, true);
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
