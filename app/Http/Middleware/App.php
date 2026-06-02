<?php

namespace App\Http\Middleware;

use App\Utils\Agent;
use Closure;
use Illuminate\Http\Request;

define('IA_ROOT', base_path('public'));
define('QingFrame', true);
define("MICRO_SERVER", base_path("servers/"));
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('ATTACHMENT_ROOT', storage_path('app/public/'));
define('TIMESTAMP', time());
define('DEVELOPMENT', (bool)config('system.setting.development', false));
define('SITEACID', (int)config('system.site.uniacid', 0));
define('QingVersion', config('system.version'));
define('QingRelease', config('system.versionCode'));
define('QingDebug', config('system.debugMode'));

error_reporting(E_ERROR);
global $_W,$_GPC;
$_W = $_GPC = array();

class App
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->initialize($request);
        return $next($request);
    }

    public function getRealIp(Request $request)
    {
        $headers = $request->header();
        $cdnIpHeadName = config('system.cdn.header_column');
        if (!empty($headers[$cdnIpHeadName])){
            return $headers[$cdnIpHeadName][0];
        }
        return $request->getClientIp();
    }

    public function initialize(Request $request){
        global $_W,$_GPC;
        $_GPC = $request->all();
        $_W['session_id'] = $request->session()->getId();
        $_W['startTime'] = microtime(true);
        $_W['config'] = config('system');
        $_W['setting'] = [];
        $_W['framework'] = ['version'=>QingVersion, 'release'=>QingRelease];
        $_W['timestamp'] = TIMESTAMP;
        $_W['charset'] = $_W['config']['setting']['charset'];
        $_W['clientip'] = $this->getRealIp($request);
        $_W['isajax'] = $request->ajax() || !empty($_GPC['inajax']);
        $_W['ispost'] = $request->isMethod('post');
        $query = http_build_query($_GET, '', '&');
        $_W['siteurl'] = url()->current() . ($query ? "?".$query : "");
        $_W['ishttps'] = (bool)config('system.setting.force_https');
        if ($_W['ishttps']){
            $_W['siteurl'] = str_replace("http://", "https://", $_W['siteurl']);
        }else{
            $_W['ishttps'] = \Str::startsWith($_W['siteurl'],'https');
        }
        $_W['sitescheme'] = $_W['ishttps'] ? 'https://' : 'http://';
        $_W['siteroot'] = $_W['sitescheme'] . $_SERVER['HTTP_HOST'] .'/';
        $_W['attachurl_local'] = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/';
        $_W['siteacid'] = SITEACID;
        $_W['uniacid'] = $_W['uid'] = 0;
        $_W['user'] = array('uid'=>$_W['uid'],'username'=>'未登录');
        $_W['account'] = array('uniacid'=>0);
        $_W['inConsole'] = $_W['inApp'] = $_W['inAccount'] = false;
        $_W['token'] = csrf_token();
        $_W['os'] = Agent::getOs();
        $_W['routePath'] = $request->path();
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($_W['config']['setting']['timezone']);
        }
        if (config('app.debug')){
            ini_set('display_errors', '1');
            //error_reporting(E_ALL ^ E_NOTICE);
        }
        $appLocale = config('app.locale');
        $_W['locale'] = session()->get("FRAME_LOCALE", $appLocale);
        if ($appLocale!=$_W['locale']){
            \Illuminate\Support\Facades\App::setLocale($_W['locale']);
        }
    }

}
