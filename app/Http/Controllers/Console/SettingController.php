<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\CloudService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //
    public function active(){
        global $_W;
        $loadcomponent = CloudService::RequireCom();
        if (is_error($loadcomponent)) return $this->message($loadcomponent['message']);
        dd($loadcomponent);
        $activestate = CloudService::CloudActive();
        if ($activestate['status']==1 && $_W['config']['site']['id']==0){
            //从云端获取组件
            $loadcomponent = CloudService::RequireCom();
            if (is_error($loadcomponent)) return $this->message($loadcomponent['message']);
            dd($loadcomponent);
            //从云端获取默认模块并进行安装
            //从云端获取SOCKET安装包
            //更新siteid
        }
        return $this->globalview('welcome');
    }

}
