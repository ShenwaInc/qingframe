<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function message($msg='操作失败，请重试',$redirect='',$type='error'){
        global $_W;
        $data = array('message'=>$msg,'redirect'=>$redirect,'type'=>$type);
        if ($_W['isajax']){
            ob_clean();
            echo json_encode($data);
            exit();
        }
    }

}
