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

    public static function user_role_insert($uniacid,$uid,$role='owner'){
        return DB::table(self::$tables['users'])->updateOrInsert(
            ['uniacid' => $uniacid, 'uid' => $uid],
            ['role'=>trim($role)]
        );
    }

}
