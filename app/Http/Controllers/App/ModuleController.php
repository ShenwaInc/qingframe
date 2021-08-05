<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class ModuleController extends Controller {
    //
    public function index($uniacid,$modulename){
        $account = Account::where('uniacid',intval($uniacid))->first();
    }

}
