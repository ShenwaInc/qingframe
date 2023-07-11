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

    public $SsoMaster = true;
    /**
     * @var mixed|string
     */
    public $WebIndex;

    /**
     * @throws Exception
     */
    public function doWebIndex(){
        global $_W;
        if (!empty($this->module['config']['WebIndex'])){
            $this->WebIndex = $this->module['config']['WebIndex'];
        }
        if (empty($this->WebIndex)){
            if ($_W['isfounder']){
                return redirect(wurl("m/".$this->modulename."/setting"));
            }
            throw new Exception('Undefined WebIndex :' . $this->modulename, E_USER_WARNING);
        }
        $SsoService = serv('sso');
        if (!$SsoService->enabled){
            throw new Exception('单点登录服务不可用', E_USER_WARNING);
        }
        $code = $this->SsoMaster ? $SsoService->getMasterCode() : $SsoService->getCode();
        if (is_error($code)){
            throw new Exception($code['message'], E_USER_WARNING);
        }
        if (strpos($this->WebIndex, '#')){
            $this->WebIndex = explode("#", $this->WebIndex)[0];
        }
        $redirect = $this->WebIndex . (strpos($this->WebIndex,'?') ? '&' : '?') . "code=$code&uniacid=" . $this->uniacid;
        /**
         * 暂未支持iFrame模式，待完善
        $openType = trim($this->module['config']['openType']);
        if ($openType=='iframe'){
            View::share('_W',$_W);
            return view('console.module.iframe', ['configs'=>$this->module['config'], 'src'=>$redirect, 'title'=>$this->module['title']]);
        }
        */
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
