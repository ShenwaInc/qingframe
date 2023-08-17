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

    public function message($prompt='operationFailed', $redirect='', $type='error', $extra=array()){
        global $_W;
        if (is_string($prompt) && preg_match('/^([\w\s]+)$/', $prompt)){
            $prompt = __($prompt);
        }
        if ($redirect && isset($extra['light']) && $extra['light']!=''){
            $prompt = str_replace($extra['light'],'<a href="'.$redirect.'" class="message-light">'.$extra['light'].'</a>',$prompt);
        }
        $return = array('redirect'=>$redirect,'type'=>$type, 'code'=>$extra['code']??0, 'message'=>$prompt, 'data'=>[]);
        if (is_array($prompt)){
            $return['message'] = 'OK';
            $return['data'] = $prompt;
        }
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

    public function success($message="successful", $redirect=""){
        return $this->message($message, $redirect, "success");
    }

    public function globalView($view, $data=array()){
        global $_W,$_GPC;
        if (empty($data)) $data = [];
        $data['_W'] = $_W;
        $data['_GPC'] = $_GPC;
        if (is_array($view)){
            return view()->first($view, $data);
        }
        return \view($view, $data);
    }

}
