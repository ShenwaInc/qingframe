<?php

namespace App\Services;

use App\Models\Account;
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

    static function GetOprateStar($uid,$uniacid,$module_name){
        return DB::table('users_operate_star')->where(array(
            ['uid',$uid],
            ['uniacid',$uniacid],
            ['module_name',$module_name]
        ))->first();
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

}
