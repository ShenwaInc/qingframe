<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    //
    public function index(){
        $uniacid = DB::table("site_multi")->where('bindhost',trim($_SERVER['HTTP_HOST']))->value('uniacid');
        if ($uniacid){
            //已知平台
        }else{
            //未知平台
            //abort(403, '无效的后台入口');
        }
    }

}
