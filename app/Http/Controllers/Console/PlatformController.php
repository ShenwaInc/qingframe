<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    //
    public function index(){
        return view('welcome');
    }

    public function account($uniacid){
        global $_W,$_GPC;
        print_r($_W);
        print_r($_GPC);
        die($uniacid);
    }

    public function utils($uniacid){
        die();
    }

    public function payment(){
    }
}
