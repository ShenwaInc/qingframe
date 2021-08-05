<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //
    protected $table = 'uni_account';
    public $tables = array(
        'aliapp'=>'account_aliapp',
        'baiduapp'=>'account_baiduapp',
        'wechat'=>'account_wechats',
        'wxapp'=>'account_wxapp',
        'modules'=>'uni_account_extra_modules',
        'moduleset'=>'uni_account_modules',
        'users'=>'uni_account_users'
    );
}
