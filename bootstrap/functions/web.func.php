<?php

use Illuminate\Support\Facades\View;

function message($msg, $redirect = '', $type = '') {
    global $_W, $_GPC;
    $data = array('message'=>$msg,'redirect'=>$redirect,'type'=>$type);
    ob_clean();
    if ($_W['isajax']){
        echo json_encode($data);
    }else{
        View::share('_W',$_W);
        View::share('_GPC',$_GPC);
        echo response()->view('message',$data)->content();
    }
    session()->save();
    exit;
}
