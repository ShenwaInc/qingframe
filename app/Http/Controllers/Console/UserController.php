<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index(Request $request,$op='index'){
        global $_W;
        $method = "do{$op}";
        if (method_exists($this,$method)){
            return $this->$method($request);
        }
    }

    public function doprofile(Request $request){
        global $_W;
        return $this->globalview('console.user.profile');
    }

}
