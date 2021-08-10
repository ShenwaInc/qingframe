<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\This;

class Setting extends Model
{
    //
    protected $table = 'core_settings';
    public static $table_uni = 'uni_settings';

    public static function uni_save($uniacid,$key,$value){
        return DB::table(self::$table_uni)->updateOrInsert(['uniacid'=>$uniacid],[$key=>$value]);
    }
}
