<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Utils\WeModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    //

    public function entry(Request $request, $modulename,$do='index'){
        global $_W;
        $WeModule = new WeModule();
        try {
            $site = $WeModule->create($modulename);
        }catch (\Exception $exception){
            return $this->message('模块初始化失败，请联系技术处理');
        }
        $method = "doWeb" . ucfirst($do);
        if (!method_exists($site,$method)){
            return $this->message("模块不支持{$method}()方法");
        }
        DB::table('users_operate_history')->updateOrInsert(
            array('uid'=>$_W['uid'],'uniacid'=>$_W['uniacid'],'module_name'=>$modulename),
            array('createtime'=>TIMESTAMP,'type'=>2)
        );
        if (!function_exists('message')){
            require_once base_path('bootstrap/functions/web.func.php');
        }
        return $site->$method($request);
    }

}
