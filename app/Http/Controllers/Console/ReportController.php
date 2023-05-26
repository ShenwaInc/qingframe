<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller {

    public function httpReq(Request $request, $option='index'){
        return $this->$option($request);
    }

    public function index($request){
        dd($request->all());
    }

}
