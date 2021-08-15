<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniAccountUser extends Model
{
    //
    protected $table = 'uni_account_users';

    public static function AddRole($uniacid,$uid,$role='owner'){
        return self::updateOrInsert(
            ['uniacid' => $uniacid, 'uid' => $uid],
            ['role'=>trim($role)]
        );
    }

}
