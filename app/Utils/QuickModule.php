<?php

namespace App\Utils;

use Exception;
use Illuminate\Support\Facades\View;

class QuickModule
{

    public $module;

    public $modulename;

    public $weid;

    public $uniacid;

    public $__define;

    /**
     * @throws Exception
     */
    public function doWebIndex(){
        global $_W;
        if (empty($this->WebIndex)){
            if ($_W['isfounder']){
                return redirect(wurl("m/".$this->modulename."/setting"));
            }
            throw new Exception('Undefined WebIndex :' . $this->modulename, E_USER_WARNING);
        }
        if (!serv('sso')->enabled){
            throw new Exception('单点登录服务不可用', E_USER_WARNING);
        }
        $code = serv('sso')->getCode();
        if (is_error($code)){
            throw new Exception($code['message'], E_USER_WARNING);
        }
        $redirect = $this->WebIndex . (strpos($this->WebIndex,'?') ? '&' : '?') . "code=$code&uniacid=" . $this->uniacid;
        return redirect($redirect);
    }

    public function doWebSetting(){
        global $_W;
        if (checksubmit()){
            $data = request()->input('data');
            if (empty($data['WebIndex'])){
                message("无效的第三方应用入口链接");
            }
            $WeModule = new WeModule();
            $WeModule->modulename = $this->modulename;
            if (!$WeModule->saveSettings($data)){
                message("保存失败，请重试");
            }
            return redirect(wurl("m/".$this->modulename));
        }
        View::share('_W',$_W);
        return view('console.module.quickSetting', ['configs'=>$this->module['config'], 'title'=>'应用配置']);
    }

}
