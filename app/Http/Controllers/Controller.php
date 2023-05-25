<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function message($msg='操作失败，请重试',$redirect='',$type='error',$extra=array()){
        global $_W;
        if ($redirect && isset($extra['light']) && $extra['light']!=''){
            $msg = str_replace($extra['light'],'<a href="'.$redirect.'" class="message-light">'.$extra['light'].'</a>',$msg);
        }
        $return = array('redirect'=>$redirect,'type'=>$type, 'code'=>$extra['code']??0);
        $return['message'] = is_array($msg) ? 'OK' : $msg;
        $return['data'] = is_array($msg) ? $msg : [];
        if(!isset($_W['isajax'])){
            $_W['isajax'] = \request()->ajax() || \request('inajax', 0);
        }
        if ($_W['isajax'] || $_W['isapi']){
            return response()->json($return);
        }else{
            $view = defined("IN_MOBILE") ? 'mmessage' : "message";
            return $this->globalView($view, $return);
        }
    }

    public function globalView($view, $data=array()){
        global $_W,$_GPC;
        View::share('_W',$_W);
        View::share('_GPC',$_GPC);
        return view($view,$data);
    }

}
