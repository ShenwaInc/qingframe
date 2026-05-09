<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{

    function index(Request $request){
        $domain = $_SERVER['HTTP_HOST'];
        $domainApp = env('APP_URL', '');
        if (!strexists($domainApp, $domain)){
            //绑定域名自动处理
            $bindUniacid = DB::table('uni_settings')->where('bind_domain', $domain)->value('uniacid');
            if (!empty($bindUniacid)){
                if (empty($request->user())){
                    return redirect("/login/$bindUniacid");
                }
                return redirect("/console/account/$bindUniacid");
            }
        }

        $redirect = wurl('');
        if (!empty($_SERVER['QUERY_STRING'])){
            $redirect .= '?' . $_SERVER['QUERY_STRING'];
        }
        header('Location: ' . $redirect);
        exit();
    }

}
