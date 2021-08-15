<?php


namespace App\Services;


use App\Models\Account;

class AccountService
{

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

    static function Create_info() {
        global $_W;
        $account_create_info = PermissionService::UserAccountNum();
        if ($_W['isadmin']) {
            $sign_info['can_create'] = true;
        }
        $can_create = false;
        if ($_W['isadmin'] || (!empty($account_create_info['account_limit']) && (!empty($account_create_info['founder_account_limit']) && $_W['user']['owner_uid'] || empty($_W['user']['owner_uid'])) || !empty($account_create_info['store_account_limit']))){
            $can_create = true;
        }
        $all_account_type_sign = array(
            'account' => array(
                'contain_type' => array(1, 3),
                'level' => array(1 => '订阅号', 2 => '服务号', 3 => '认证订阅号', 4 => '认证服务号'),
                'icon' => 'wi wi-wx-circle',
                'createurl' => url('account/post'),
                'title' => '公众号',
                'can_create' => $can_create
            )
        );
        return $all_account_type_sign;
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
            $uni_account_users_table = table('uni_account_users');
            $uni_account_users_table->searchWithRole($role);
            $all_account = $uni_account_users_table->getCommonUserOwnAccountUniacids($uid);

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
