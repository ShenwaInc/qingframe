<?php

namespace App\Services;

use App\Models\UniAccountUser;
use App\User;
use Illuminate\Support\Facades\DB;


class UserService
{

    static function IsExists($user){
        if (empty($user) || !is_array($user)) {
            return false;
        }
        $where = ' WHERE 1 ';
        $params = array();
        if (!empty($user['uid'])) {
            $where .= ' AND `uid`=:uid';
            $params[':uid'] = intval($user['uid']);
        }
        if (!empty($user['username'])) {
            $where .= ' AND `username`=:username';
            $params[':username'] = $user['username'];
        }
        if (!empty($user['status'])) {
            $where .= " AND `status`=:status";
            $params[':status'] = intval($user['status']);
        }
        if (empty($params)) {
            return false;
        }
        $sql = 'SELECT `password`,`salt` FROM ' . tablename('users') . "$where LIMIT 1";
        $record = pdo_fetch($sql, $params);
        if (empty($record) || empty($record['password']) || empty($record['salt'])) {
            return false;
        }
        if (!empty($user['password'])) {
            $password = self::GetHash($user['password'], $record['salt']);
            return $password == $record['password'];
        }
        return true;
    }

    static function GetHash($passwordinput, $salt, $authkey=''){
        global $_W;
        if (empty($authkey) && isset($_W['config'])){
            $authkey = $_W['config']['setting']['authkey'];
        }
        $passwordinput = "{$passwordinput}-{$salt}-{$authkey}";
        return sha1($passwordinput);
    }

    static function GetOne($user_or_uid){
        $user = $user_or_uid;
        if (empty($user)) {
            return false;
        }
        if (is_numeric($user)) {
            $user = array('uid' => $user);
        }
        if (!is_array($user)) {
            return false;
        }
        $where = ' WHERE 1 ';
        $params = array();
        if (!empty($user['uid'])) {
            $where .= ' AND u.`uid`=:uid';
            $params[':uid'] = intval($user['uid']);
        }
        if (!empty($user['username'])) {
            $where .= ' AND u.`username`=:username';
            $params[':username'] = $user['username'];

            $user_exists = self::IsExists($user);
            $is_mobile = preg_match('/1[3456789][0-9]{9}/', $user['username']);
            if (!$user_exists && !empty($user['username']) && $is_mobile) {
                $sql = "select b.uid, u.username FROM " . tablename('users_bind') . " AS b LEFT JOIN " . tablename('users') . " AS u ON b.uid = u.uid WHERE b.bind_sign = :bind_sign";
                $bind_info = pdo_fetch($sql, array('bind_sign' => $user['username']));
                if (!is_array($bind_info) || empty($bind_info) || empty($bind_info['username'])) {
                    return false;
                }
                $params[':username'] = $bind_info['username'];
            }
        }
        if (!empty($user['email'])) {
            $where .= ' AND u.`email`=:email';
            $params[':email'] = $user['email'];
        }
        if (!empty($user['status'])) {
            $where .= " AND u.`status`=:status";
            $params[':status'] = intval($user['status']);
        }
        if (empty($params)) {
            return false;
        }
        $sql = 'SELECT u.*, p.avatar FROM ' . tablename('users') . ' AS u LEFT JOIN '. tablename('users_profile') . ' AS p ON u.uid = p.uid '. $where. ' LIMIT 1';

        $record = pdo_fetch($sql, $params);
        if (empty($record)) {
            return false;
        }
        if (!empty($user['password'])) {
            $password = self::GetHash($user['password'], $record['salt']);
            if ($password != $record['password']) {
                return false;
            }
        }

        $record['hash'] = md5($record['password'] . $record['salt']);
        unset($record['password'], $record['salt']);
        $founder_own_user_info = DB::table('users_founder_own_users')->where('uid',$user['uid'])->first();
        if (!empty($founder_own_user_info) && !empty($founder_own_user_info['founder_uid'])) {
            $vice_founder_info = pdo_getcolumn('users', array('uid' => $founder_own_user_info['founder_uid']), 'username');
            if (!empty($vice_founder_info)) {
                $record['vice_founder_name'] = $vice_founder_info;
            } else {
                pdo_delete('users_founder_own_users', array('founder_uid' => $founder_own_user_info['founder_uid'], 'uid' => $founder_own_user_info['uid']));
            }
        }
        if($record['type'] == 3) {
            $clerk = pdo_get('activity_clerks', array('uid' => $record['uid']));
            if(!empty($clerk)) {
                $record['name'] = $clerk['name'];
                $record['clerk_id'] = $clerk['id'];
                $record['store_id'] = $clerk['storeid'];
                $record['store_name'] = pdo_fetchcolumn('SELECT business_name FROM ' . tablename('activity_stores') . ' WHERE id = :id', array(':id' => $clerk['storeid']));
                $record['clerk_type'] = '3';
                $record['uniacid'] = $clerk['uniacid'];
            }
        } else {
            $record['name'] = $record['username'];
            $record['clerk_id'] = $user['uid'];
            $record['store_id'] = 0;
            $record['clerk_type'] = '2';
        }
        $third_info = pdo_getall('users_bind', array('uid' => $record['uid']), array(), 'third_type');
        if (!empty($third_info) && is_array($third_info)) {
            $record['qq_openid'] = $third_info[1]['bind_sign'];
            $record['wechat_openid'] = $third_info[2]['bind_sign'];
            $record['mobile'] = $third_info[3]['bind_sign'];
        }
        $record['notice_setting'] = unserialize($record['notice_setting']);
        return $record;
    }

    static function GetSubs($uid=0){
        if (empty($uid)){
            global $_W;
            $uid = $_W['uid'];
        }
        if (!$uid) return array();
        return DB::table('users')->where(array('owner_uid'=>$uid,'status'=>2))->get()->keyBy('uid')->toArray();
    }

    static function isFounder($uid, $only_main_founder = false){
        global $_W;
        $founders = array($_W['config']['setting']['founder']);
        if (strpos((string)$_W['config']['setting']['founder'],',')!==false){
            $founders = explode(',', $_W['config']['setting']['founder']);
        }
        if (in_array($uid, $founders)) {
            return true;
        }
        if (empty($only_main_founder)) {
            $founder_groupid = User::where('uid',$uid)->value('founder_groupid');
            if ($founder_groupid == 2) {
                return true;
            }
        }
        return false;
    }

    static function isVicefounder($uid = 0) {
        global $_W;
        $uid = intval($uid);
        if (empty($uid)) {
            $user_info = $_W['user'];
        } else {
            $user_info = User::where('uid',$uid)->first();
        }
        if ($user_info['founder_groupid'] == 2) {
            return true;
        }
        return false;
    }

    static function AccountRole($uid = 0, $uniacid = 0){
        global $_W;
        $role = '';
        $uid = empty($uid) ? $_W['uid'] : intval($uid);
        if (self::isFounder($uid,true)){
            return 'founder';
        }else{
            $user_info = User::where('uid',$uid)->get();
            if (!empty($user_info['endtime']) && $user_info['endtime'] != 0 && $user_info['endtime'] != 2 && $user_info['endtime'] < TIMESTAMP) {
                return 'expired';
            }
            if ($user_info['type'] == 3) {
                return 'clerk';
            }
        }

        if (!empty($uniacid)) {
            $role = (string)UniAccountUser::where(array('uid' => $uid, 'uniacid' => $uniacid))->value('role');
            if (in_array($role, array('owner','vice_founder','manager','operator','clerk'))){
                return $role;
            }
            return $role;
        } else {
            if ($user_info['founder_groupid']==2){
                return 'vice_founder';
            }

            $roles = UniAccountUser::where(array('uid' => $uid))->get(['role'])->toArray();;
            $roles = array_keys($roles);
            if (in_array('vice_founder', $roles)) {
                $role = 'vice_founder';
            } elseif (in_array('owner', $roles)) {
                $role = 'owner';
            } elseif (in_array('manager', $roles)) {
                $role = 'manager';
            } elseif (in_array('operator', $roles)) {
                $role = 'operator';
            }
        }
        $role = empty($role) ? $user_info['founder_groupid']==2 ? 'vice_founder' : 'owner' : $role;
        return $role;
    }

    static function AccountRoleUpdate($uniacid,$uid,$role='owner'){
        return DB::table('uni_account_users')->updateOrInsert(
            ['uniacid' => $uniacid, 'uid' => $uid],
            ['role'=>trim($role)]
        );
    }

}
