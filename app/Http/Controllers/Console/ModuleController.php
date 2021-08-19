<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Utils\WeModule;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    //

    public function entry(Request $request, $modulename,$do='index'){
        $WeModule = new WeModule();
        $site = $WeModule->create($modulename);
        $method = "doWeb" . ucfirst($do);
        return $site->$method();
    }

}
