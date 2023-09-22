<?php

namespace App\Http\Middleware;

use App\Utils\Agent;
use Closure;
use Illuminate\Support\Facades\Cache;

error_reporting(0);
define('IA_ROOT', base_path('public'));
define('BASE_ROOT', base_path('/'));
define('QingFrame', true);
define("MICRO_SERVER", base_path("servers/"));
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('ATTACHMENT_ROOT', storage_path('app/public/'));
define('TIMESTAMP', time());
define('DEVELOPMENT', (bool)env('APP_DEVELOPMENT',0));
define('SITEACID', env('APP_UNIACID', 0));
define('QingVersion', env('APP_VERSION'));
define('QingRelease', env('APP_RELEASE'));
global $_W,$_GPC;
$_W = $_GPC = array();

class App
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        global $_W,$_GPC;
        $_GPC = $request->all();
        $_W['startTime'] = microtime(true);
        $_W['config'] = config('system');
        $_W['framework'] = ['version'=>QingVersion, 'release'=>QingRelease];
        $_W['timestamp'] = TIMESTAMP;
        $_W['charset'] = $_W['config']['setting']['charset'];
        $_W['clientip'] = $request->getClientIp();
        $_W['isajax'] = $request->ajax() || !empty($_GPC['inajax']);
        $_W['ispost'] = $request->isMethod('post');
        $query = http_build_query($_GET, '', '&');
        $_W['siteurl'] = url()->current() . ($query ? "?".$query : "");
        $_W['ishttps'] = \Str::startsWith($_W['siteurl'],'https');
        $_W['sitescheme'] = $_W['ishttps'] ? 'https://' : 'http://';
        $_W['siteroot'] = $_W['sitescheme'] . $_SERVER['HTTP_HOST'] .'/';
        $_W['siteacid'] = SITEACID;
        $_W['uniacid'] = $_W['uid'] = 0;
        $_W['user'] = array('uid'=>$_W['uid'],'username'=>'未登录');
        $_W['account'] = array('uniacid'=>0);
        $_W['inConsole'] = $_W['inapp'] = false;
        $_W['token'] = csrf_token();
        $_W['os'] = Agent::getOs();
        $_W['routePath'] = $request->path();
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($_W['config']['setting']['timezone']);
        }
        $appLocale = config('app.locale');
        $_W['locale'] = session()->get("FRAME_LOCALE", $appLocale);
        if ($appLocale!=$_W['locale']){
            \Illuminate\Support\Facades\App::setLocale($_W['locale']);
        }
        return $next($request);
    }

}
