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
        $data = array('message'=>$msg,'redirect'=>$redirect,'type'=>$type, '_W'=>$_W);
        if ($_W['isajax']){
            return json_encode($data);
        }else{
            return response()->view('message', $data, 200)->header('Content-Type','text/html; charset=UTF-8')->content();
        }
    }

    public function globalview($view,$data=array()){
        global $_W,$_GPC;
        View::share('_W',$_W);
        View::share('_GPC',$_GPC);
        return view($view,$data);
    }

}
