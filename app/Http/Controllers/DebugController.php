<?php

namespace App\Http\Controllers;

class DebugController extends Controller
{
    public function index()
    {
        //$nonceStr = random(8);
        //$appSecret = 's5QwGyF7nZip2Zm3FSsbxmruZkwPRxFN';
        //token = md5($appSecret . $nonceStr);



        $state = 'eyJ1aWQiOjIxLCJleHBpcmUiOjE3NzMyODczNzEsImhhc2giOiIyNzI5YTFkZjc1OTZjZjQxZGNmZDZiMjQyOTQ0M2ZiMyJ9';
        $state_decode = base64_decode($state);
        $authInfo = json_decode($state_decode, true);
        $authInfo['expire_at'] = date('Y-m-d H:i:s', $authInfo['expire']);
        dd($state, $state_decode, $authInfo);
    }
}
