<?php

namespace App\Http\Controllers;

use App\Http\Middleware\App;
use App\Models\SystemLog;
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
                SystemLog::systemRunning(
                    '数据库表检查异常',
                    'home:index',
                    "检查系统安装状态时发生异常：{$exception->getMessage()}",
                    false,
                    [
                        'exception_file' => $exception->getFile(),
                        'exception_line' => $exception->getLine(),
                        'exception_code' => $exception->getCode(),
                        'exception_trace' => $exception->getTrace(),
                    ]
                );
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
        $locale = e($request->input('lang', $_W['locale']));
        if (!empty($locale) && $locale!=$_W['locale']){
            $language = serv('language');
            if ($language->enabled){
                $languages = $language->langUsable();
                if (!empty($languages) && !empty($languages[$locale])){
                    \Illuminate\Support\Facades\App::setLocale($locale);
                    session()->put('FRAME_LOCALE', $locale);
                }
            }
        }
        SettingService::Load();
        $language = serv('language');
        $views = ['welcomeCustom', 'welcome'];
        if (!empty($uniacid)){
            $views = ['welcomeCustom'.$uniacid, 'welcomeCustom', 'welcome'];
        }
        $user = $request->user();
        if (!empty($user)){
            $profile = DB::table('users_profile')->where('uid', $user->uid)->select('avatar','gender','mobile','email', 'realname')->first();
            $_W['user'] = array_merge($user->toArray(), $profile?:[]);
            $_W['uid'] = $user->uid;
        }
        return response()->view($views, array('title'=>__($_W['setting']['page']['title']), '_W'=>$_W, 'Multilingual'=>$language->enabled, 'locale'=>$locale));
    }

    public function module(Request $request, $moduleName){
        if (empty($request->user())){
            $referer = "/login?referer=console/m/$moduleName";
            return response()->redirectTo($referer);
        }
        return response()->redirectTo("console/m/$moduleName");
    }

}
