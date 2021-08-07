<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    //
    public function Main($uniacid,$modulename){
        $route = \request('r');
        die($route);
    }

    public function entry($uniacid,$modulename){
        $route = \request('r');
        die($route);
    }

}
