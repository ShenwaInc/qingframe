<?php


namespace App\Services;


use App\Models\Account;
use App\User;
use Illuminate\Support\Facades\DB;

class PermissionService {

    static function UserAccountNum($uid = 0) {
        global $_W;
        $uid = intval($uid);
        $user = $uid > 0 ? UserService::GetOne($uid) : $_W['user'];
        if (empty($user)) {
            return array();
        }
        $user_founder_info = DB::table('users_founder_own_users')->where('uid',$user['uid'])->first();
        $account_all_type = AccountService::GetType();
        $account_all_type_sign = array('account');
        $extra_group_table = DB::table('users_extra_group');
        $extra_limit_table = DB::table('users_extra_limit');

        $isVicefounder = UserService::isVicefounder($user['uid']);

        if ($isVicefounder || !empty($user_founder_info['founder_uid'])) {
            if (!empty($user_founder_info['founder_uid'])  && !$isVicefounder) {
                $role = 'owner';
                $group = DB::table('users_group')->where('id',$user['groupid'])->first();
                $user_uid = $user_founder_info['founder_uid'];
            } else {
                $role = 'vice_founder';
                $group = DB::table('users_founder_group')->where('id',$user['groupid'])->first();
                $user_uid = $user['uid'];
            }

            foreach ($account_all_type_sign as $type_info) {
                $key_name = $type_info . '_num';
                $group_num[$key_name] = 0;
            }
            $fouder_own_users_owner_account = Account::searchAccountList(false, 1, $fields = 'uni_account.uniacid, account.type', $user['uid']);
            $current_vice_founder_user_group_nums = 0;
            if (!empty($fouder_own_users_owner_account)) {
                foreach ($fouder_own_users_owner_account as $account) {
                    foreach ($account_all_type as $type_key => $type_info) {
                        if ($type_key == $account['type']) {
                            $key_name = $type_info['type_sign'] . '_num';
                            $group_num[$key_name] += 1;
                            $current_vice_founder_user_group_nums += 1;
                            continue;
                        }
                    }
                }
            }
        } else {
            $role = 'owner';
            $group = DB::table('users_group')->where('id',$user['groupid'])->first();
            $group_num = AccountService::OwnerAccountNums($user['uid'], $role);
            if (empty($_W['isfounder'])) {
                if (!empty($user['owner_uid'])) {
                    $owner_info = User::where('uid',$user['owner_uid'])->first();
                    $group_vice = DB::table('users_founder_group')->where('id',$owner_info['groupid'])->first();

                    $founder_group_num = AccountService::OwnerAccountNums($owner_info['uid'], 'vice_founder');
                    foreach ($account_all_type_sign as $sign) {
                        $maxsign = 'max' . $sign;
                        $group[$maxsign] = min(intval($group[$maxsign]), intval($group_vice[$maxsign]));
                    }
                }
            }
        }
        if (!empty($user_founder_info['founder_uid'])) {
            $owner_info = User::where('uid',$user_founder_info['founder_uid'])->first();
            $group_vice = DB::table('users_founder_group')->where('id',$owner_info['groupid'])->first();
            $founder_group_num = AccountService::OwnerAccountNums($owner_info['uid'], 'vice_founder');
        }
        $store_create_table = DB::table('site_store_create_account');
        $create_buy_num['account'] = $store_create_table->leftJoin('account','site_store_create_account.uniacid','=','account.uniacid')->where(array(
            ['site_store_create_account.uid',$user['uid']],
            ['site_store_create_account.type','account']
        ))->count();
        $store_buy['account'] = 0;

        $extra_create_group_info  = array_keys($extra_group_table->where(array(
            ['uid',$user['uid']],
            ['create_group_id','>',0]
        ))->get()->keyBy('create_group_id')->toArray());
        $extra_limits_info = $extra_limit_table->where('uid',$user['uid'])->first();
        if (!empty($user_founder_info['founder_uid'])) {
            $founder_extra_create_group_info  = array_keys($extra_group_table->where(array(
                ['uid',$user_founder_info['founder_uid']],
                ['create_group_id','>',0]
            ))->get()->keyBy('create_group_id')->toArray());
            $founder_extra_limits_info = $extra_limit_table->where('uid',$user_founder_info['founder_uid'])->first();

            $vice_founder_own_users_create_accounts = Account::searchAccountList(false, 1, $fields = 'uni_account.uniacid, account.type', $user_founder_info['founder_uid']);
            $vice_founder_own_users_create_nums = array();
            foreach ($account_all_type_sign as $type_info) {
                $key_name = $type_info . '_num';
                $vice_founder_own_users_create_nums[$key_name] = 0;
            }
            if (!empty($vice_founder_own_users_create_accounts)) {
                foreach ($vice_founder_own_users_create_accounts as $vice_founder_own_users_create_account){
                    foreach ($account_all_type as $type_key => $type_info) {
                        if ($vice_founder_own_users_create_account['type'] == $type_key) {
                            $key_name = $type_info['type_sign'] . '_num';
                            $vice_founder_own_users_create_nums[$key_name] += 1;
                            continue;
                        }
                    }
                }
            }

        }
        $create_group_info_all = array();
        $create_group_table = DB::table('users_create_group');
        if (!empty($extra_create_group_info)) {
            $create_groups = array();
            foreach($extra_create_group_info as $create_group_id) {
                $create_group_info = $create_group_table->where('id',$create_group_id)->first();
                $create_groups[] = $create_group_info;
                foreach ($account_all_type_sign as $sign) {
                    $maxsign = 'max' . $sign;
                    $create_group_info_all[$maxsign] += $create_group_info[$maxsign];
                }
            }
        }
        $founcder_create_group_info_all = array();
        if (!empty($user_founder_info['founder_uid']) && !empty($extra_create_group_info)) {
            $founder_create_groups = array();
            foreach($founder_extra_create_group_info as $create_group_id) {
                $create_group_info = $create_group_table->where('id',$create_group_id)->first();
                $founder_create_groups[] = $create_group_info;
                foreach ($account_all_type_sign as $sign) {
                    $maxsign = 'max' . $sign;
                    $founcder_create_group_info_all[$maxsign] += $create_group_info[$maxsign];
                }
            }
        }
        $extra = $limit = $founder_limit = array();
        $founder_limit_total = 0;

        foreach ($account_all_type_sign as $sign) {
            $maxsign = 'max' . $sign;
            $extra[$sign] = $create_group_info_all[$maxsign] + $extra_limits_info[$maxsign];
            if (!empty($user_founder_info['founder_uid'])){
                $founder_extra[$sign] = $founcder_create_group_info_all[$maxsign] + $founder_extra_limits_info[$maxsign];
            } else {
                $founder_extra[$sign] = 0;
            }
            $sign_num = $sign . '_num';
            $limit[$sign] = max((intval($group[$maxsign]) + $extra[$sign] + intval($store_buy[$sign]) - $group_num[$sign_num]), 0);
            $founder_limit[$sign] = max((intval($group_vice[$maxsign]) + $founder_extra[$sign]), 0);

            if (!empty($vice_founder_own_users_create_nums)) {
                $founder_limit[$sign] -= $vice_founder_own_users_create_nums[$sign_num];
            }
            $founder_limit_total += $founder_limit[$sign];
        }
        $founder_limit_total = max(0, $founder_limit_total);
        $data = array(
            'group_name' => $group['name'],
            'vice_group_name' => $group_vice['name'],
            'create_groups' => $create_groups,
            'founder_limit_total' => $founder_limit_total,
        );
        $data['max_total'] = 0;
        $data['created_total'] = 0;
        $data['limit_total'] = 0;
        foreach ($account_all_type_sign as $sign) {
            $data["store_buy_{$sign}"] = $store_buy[$sign];
            $data["store_{$sign}_limit"] = intval($store_buy[$sign]) - intval($create_buy_num[$sign]) <= 0 ? 0 : intval($store_buy[$sign]);
            $data['store_limit_total' ] += $data["store_{$sign}_limit"];

            $maxsign = 'max' . $sign;
            $sign_num = $sign . '_num';
            $data['user_group_max' . $sign] = $group[$maxsign];
            $data['usergroup_' . $sign . '_limit'] = max($group[$maxsign] - $group_num[$sign_num] - intval($create_buy_num[$sign]), 0);
            $data[$maxsign] = $group[$maxsign] + intval($store_buy[$sign]) + $extra[$sign];
            $data[$sign_num] = $group_num[$sign_num];
            $data[$sign . '_limit'] = max($limit[$sign], 0);
            $data['extra_' . $sign] = $extra_limits_info[$maxsign];
            $data['founder_' . $sign . '_limit'] = max($founder_limit[$sign], 0);
            $data['max_total'] = $data[$maxsign] + $data['max_total'];
            $data['created_total'] = $data[$sign_num] + $data['created_total'];
            $data['limit_total'] = $data[$sign . '_limit'] + $data['limit_total'];
            $data['current_vice_founder_user_created_total'] = !empty($current_vice_founder_user_group_nums) ? $current_vice_founder_user_group_nums : 0;
            if (!empty($vice_founder_own_users_create_nums)) {
                $data['vice_founder_own_users_' . $sign_num] = $vice_founder_own_users_create_nums[$sign_num]; 		}
        }

        if (!empty($vice_founder_own_users_create_nums)) {
            foreach ($vice_founder_own_users_create_nums as $vice_founder_own_users_create_num) {
                $data['vice_founder_own_users_created_total'] += $vice_founder_own_users_create_num; 		}
        }
        ksort($data);
        return $data;
    }

}
