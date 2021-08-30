<?php

namespace App\Models;

use App\Services\UserService;
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

    const UPDATED_AT = null;
    const CREATED_AT = null;

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

    public static function searchAccountList($expire = false, $isdeleted = 1, $fields = 'uni_account.uniacid', $uid = 0){
        return self::searchAccountQuery($expire,$isdeleted,$fields,$uid)->groupBy('uni_account.uniacid')->get()->keyBy('uniacid')->toArray();
    }

    public static function searchAccountQuery($expire = false, $isdeleted = 1, $fields = 'uni_account.uniacid', $uid = 0){
        global $_W;
        $uid = empty($uid) ? $_W['uid'] : $uid;
        $condition = array(
            ['account.isdeleted','!=',$isdeleted],
            ['uni_account.default_acid','!=',0],
            ['account.type','<',9]
        );
        if (!$_W['isfounder']){
            $condition[] = ['uni_account_users.uid','=',$uid];
        }
        if ($expire=='expire'){
            $condition[] = ['account.endtime','<',TIMESTAMP];
            $condition[] = ['account.endtime','>',2];
        }
        $query = self::leftJoin('account','uni_account.uniacid','=','account.uniacid')->leftJoin('uni_account_users','uni_account.uniacid','=','uni_account_users.uniacid')->leftJoin('users','uni_account_users.uid','=','users.uid')->where($condition);
        if ($fields!==false){
            $query = $query->select($fields);
        }
        if ($expire=='unexpire'){
            $timestamp = TIMESTAMP;
            $query = $query->whereRaw("(account.endtime=0 or account.endtime=2 or account.endtime>{$timestamp})");
        }
        if (UserService::isVicefounder($uid)){
            $users_uids = DB::table('users_founder_own_users')->where('founder_uid',$uid)->get()->keyBy('uid')->toArray();
            $users_uids = array_keys($users_uids);
            $users_uids[] = $uid;
            $query = $query->whereIn('uni_account_users.uid',$users_uids)->whereIn('uni_account_users.role',array('clerk', 'operator', 'manager', 'owner', 'vice_founder'));
        }
        return $query;
    }

    static function getByAcid($acid){
        return DB::table('account')->leftJoin('uni_account','account.uniacid','=','uni_account.uniacid')->where('account.acid',$acid)->first();
    }

    static function getByUniacid($uniacid){
        return DB::table('account')->leftJoin('uni_account','account.uniacid','=','uni_account.uniacid')->where('account.uniacid',$uniacid)->first();
    }

}
