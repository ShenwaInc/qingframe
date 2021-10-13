<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServerController extends Controller
{

    public function admin(Request $request, $server, $method='index'){
        $ServerClass = $this->create($server);
        if (is_error($ServerClass)) return $this->message($ServerClass['message']);
        $foo = "doAdmin".ucfirst($method);
        if (!method_exists($ServerClass,$foo)){
            return $this->message("该服务不支持admin/{$method}方法");
        }
        return $ServerClass->$foo($request);
    }

    /**
     * 创建服务实例
     * @param string $server 服务标识
     * @param mixed $params 初始化参数
     * @return object|mixed 服务对象
    */
    public function create($server,$params=null){
        $ServerClass = ucfirst($server).'Server';
        if (!file_exists(public_path("server/{$server}/{$ServerClass}.php"))){
            return error(-1,"{$ServerClass}服务不存在");
        }
        if (!class_exists($ServerClass)){
            require_once public_path("server/{$server}/{$ServerClass}.php");
        }
        return new $ServerClass($params);
    }

}
