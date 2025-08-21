<?php

namespace App\Http\Controllers;

use App\Http\Middleware\App;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        try {
            $installed = Schema::hasTable("account");
        }catch (\Exception $exception){
            if(in_array($exception->getCode(), [1044, 1045, 2002])){
                $installed = false;
            }else{
                throw $exception;
            }
        }
        if (!$installed){
            return response()->redirectTo('installer');
        }

        global $_W;
        $App = new App();
        $App->initialize($request);

        $uniacid = (int)DB::table('uni_settings')->where('bind_domain', $request->server('HTTP_HOST'))->value('uniacid');
        if (!empty($uniacid) && env('APP_FORCE_DOMAIN', false)){
            if (Auth::check()){
                return redirect("/console/account/{$uniacid}");
            }else{
                return redirect("/login/{$uniacid}");
            }
        }
        $locale = $request->input('lang', $_W['locale']);
        if (!empty($locale) && $locale!=$_W['locale']){
            \Illuminate\Support\Facades\App::setLocale($locale);
            session()->put('FRAME_LOCALE', $locale);
        }
        SettingService::Load();
        $language = serv('language');
        $views = ['welcomeCustom', 'welcome'];
        if (!empty($uniacid)){
            $views = ['welcomeCustom'.$uniacid, 'welcomeCustom', 'welcome'];
        }
        return response()->view($views, array('title'=>__($_W['setting']['page']['title']), 'Multilingual'=>$language->enabled, 'locale'=>$locale));
    }

    public function module(Request $request, $moduleName){
        if (empty($request->user())){
            $referer = "/login?referer=console/m/$moduleName";
            return response()->redirectTo($referer);
        }
        return response()->redirectTo("console/m/$moduleName");
    }

}
