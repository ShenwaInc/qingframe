<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    //
    protected $table = 'uni_account';
    public static $tables = array(
        'aliapp'=>'account_aliapp',
        'baiduapp'=>'account_baiduapp',
        'wechat'=>'account_wechats',
        'wxapp'=>'account_wxapp',
        'modules'=>'uni_account_extra_modules',
        'moduleset'=>'uni_account_modules',
        'users'=>'uni_account_users'
    );

    private static $accountObj = array();

    public static function account_create($uniacid, $account, $uid=0){
        global $_W;
        $type_sign = 'account';
        $accountdata = array('uniacid' => $uniacid, 'type' => 1, 'hash' => \Str::random(8));
        if (isset($_W['user'])){
            $accountdata['endtime'] = $_W['user']['endtime'];
        }
        if (isset($_W['uid']) && $uid==0){
            $uid = $_W['uid'];
        }
        if (empty($_W['isfounder']) && empty($user_create_account_info["usergroup_{$type_sign}_limit"])) {
            DB::table('site_store_create_account')->insert(array('endtime' => strtotime('+1 month', time()), 'uid' => $uid, 'uniacid' => $uniacid, 'type' => 1));
        }
        $acid = DB::table('account')->insertGetId($accountdata);

        $account['acid'] = $acid;
        $account['uniacid'] = $uniacid;
        $account['token'] = \Str::random(32);
        $account['encodingaeskey'] = \Str::random(43);
        DB::table(self::$tables['wechat'])->insert($account);
        return $acid;
    }

    static function uni_fetch($uniacid = 0) {
        global $_W;
        $uniacid = empty($uniacid) ? $_W['uniacid'] : intval($uniacid);
        $account_api = self::createByUniacid($uniacid);
        if (is_error($account_api)) {
            return $account_api;
        }
        $account_api->__toArray();
        $account_api['accessurl'] = $account_api['manageurl'] = url("console/account/{$uniacid}/post", array('account_type' => $account_api['type']), true);
        $account_api['roleurl'] = url("console/account/{$uniacid}/postuser", array('account_type' => $account_api['type']), true);
        return $account_api;
    }

    static function createByUniacid($uniacid = 0) {
        global $_W;
        $uniacid = intval($uniacid) > 0 ? intval($uniacid) : $_W['uniacid'];
        if (!empty(self::$accountObj[$uniacid])) {
            return self::$accountObj[$uniacid];
        }
        $uniaccount = self::getUniAccountByUniacid($uniacid);
        if (empty($uniaccount)) {
            return error('-1', '帐号不存在或是已经被删除');
        }
        if (!empty($_W['uid']) && !$_W['isadmin'] && !Permission::account_user_role($_W['uid'], $uniacid)) {
            return error('-1', '无权限操作该平台账号');
        }
        return self::create($uniaccount);
    }

    public static function create($acidOrAccount = array()) {
        global $_W;
        $uniaccount = array();
        if (is_object($acidOrAccount) && $acidOrAccount instanceof self) {
            return $acidOrAccount;
        }
        if (is_array($acidOrAccount) && !empty($acidOrAccount)) {
            $uniaccount = $acidOrAccount;
        } else {
            if (empty($acidOrAccount)) {
                $uniaccount = self::getUniAccountByUniacid($_W['account']['uniacid']);
            } else {
                $uniaccount = self::getUniAccountByAcid(intval($acidOrAccount));
            }
        }
        if (is_error($uniaccount) || empty($uniaccount)) {
            $uniaccount = $_W['account'];
        }
        if (!empty(self::$accountObj[$uniaccount['uniacid']])) {
            return self::$accountObj[$uniaccount['uniacid']];
        }
        if (!empty($uniaccount) && isset($uniaccount['type']) || !empty($uniaccount['isdeleted'])) {
            return self::includes($uniaccount);
        } else {
            return error('-1', '帐号不存在或是已经被删除');
        }
    }

    public static function includes($uniaccount) {
        $type = $uniaccount['type'];

        $file = self::$accountClassname[$type];
        $classname = self::getClassName($file);
        load()->classs($file);
        $account_obj = new $classname($uniaccount);
        $account_obj->type = $type;
        self::$accountObj[$uniaccount['uniacid']] = $account_obj;

        return $account_obj;
    }

    public static function searchAccountList($expire = false, $isdeleted = 1, $fields = 'a.uniacid', $uid = 0){
        global $_W;
        $uid = empty($uid) ? $_W['uid'] : $uid;
        $condition = array(
            ['account.isdeleted','!=',$isdeleted],
            ['uni_account.default_acid','!=',0],
            ['account.type','<',9],
            ['uni_account_users.uid','=',$uid]
        );
        if ($expire=='expire'){
            $condition[] = ['account.endtime','<',TIMESTAMP];
            $condition[] = ['account.endtime','>',2];
        }
        $query = self::leftJoin('account','uni_account.uniacid','=','account.uniacid')->leftJoin('uni_account_users','uni_account.uniacid','=','uni_account_users.uniacid')->leftJoin('users','uni_account_users.uid','=','users.uid')->select($fields)->where($condition);
        if ($expire==''){
            $timestamp = TIMESTAMP;
            $query = $query->whereRaw("(account.endtime=0 or account.endtime=2 or account.endtime>{$timestamp})");
        }
        return $query->groupBy('uni_account.uniacid')->get()->keyBy('uniacid')->toArray();
    }

    static function getUniAccountByAcid($acid){
        return self::leftJoin('uni_account','account.uniacid','=','uni_account.uniacid')->where('account.acid',$acid)->first()->toArray();
    }

    static function getUniAccountByUniacid($uniacid){
        return self::leftJoin('uni_account','account.uniacid','=','uni_account.uniacid')->where('account.uniacid',$uniacid)->first()->toArray();
    }

}
