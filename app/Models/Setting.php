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

    public static function getUni($uniacid){
        return DB::table(self::$table_uni)->where('uniacid',$uniacid)->first();
    }
}
