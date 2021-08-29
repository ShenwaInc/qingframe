<?php

namespace App\Models;

use App\Services\CacheService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{
    //
    protected $table = 'core_settings';
    public static $table_uni = 'uni_settings';

    public static function uni_save($uniacid,$key,$value){
        $complete = DB::table(self::$table_uni)->updateOrInsert(['uniacid'=>$uniacid],[$key=>$value]);
        if ($complete){
            $cachekey = CacheService::system_key('unisetting', array('uniacid' => $uniacid));
            Cache::forget($cachekey);
        }
        return $complete;
    }

    public static function getUni($uniacid){
        return DB::table(self::$table_uni)->where('uniacid',$uniacid)->first();
    }
}
