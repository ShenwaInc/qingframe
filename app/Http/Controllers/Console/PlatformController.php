<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;

class PlatformController extends Controller
{
    //
    public function index(){
        global $_W;
        if (empty($_W['isfounder']) && !empty($_W['user']) && ($_W['user']['status'] == 1 || $_W['user']['status'] == 3)) {
            Auth::logout();
            return $this->message('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决！');
        }
        if (($_W['setting']['site']['close'] == 1) && empty($_W['isfounder'])) {
            Auth::logout();
            return $this->message('站点已关闭，关闭原因：' . $_W['setting']['site']['closereason'], url('login'), 'error');
        }
        $account_info = AccountService::Create_info();
        return view('welcome');
    }

    public function account($uniacid){
        global $_W,$_GPC;
    }

    public function utils($uniacid){
        die();
    }

    public function payment(){
    }
}
