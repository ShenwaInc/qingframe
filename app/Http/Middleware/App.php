<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;

error_reporting(0);
define('IA_ROOT', base_path('public'));
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('ATTACHMENT_ROOT', IA_ROOT . '/attachment/');
define('TIMESTAMP', time());
define('DEVELOPMENT', env('APP_DEVELOPMENT',0));
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
        $_W['config'] = config('system');
        $dbconfig = config('database');
        $_W['config']['db'] = $dbconfig['connections'][$dbconfig['default']];
        $_W['timestamp'] = TIMESTAMP;
        $_W['charset'] = $_W['config']['setting']['charset'];
        $_W['clientip'] = $request->getClientIp();
        $_W['isajax'] = $request->ajax() || !empty($_GPC['inajax']);
        $_W['ispost'] = $request->isMethod('post');
        $_W['siteurl'] = url()->full();
        $_W['ishttps'] = \Str::startsWith($_W['siteurl'],'https');
        $_W['sitescheme'] = $_W['ishttps'] ? 'https://' : 'http://';
        $_W['siteroot'] = $_W['sitescheme'] . $_SERVER['HTTP_HOST'] .'/';
        $_W['uniacid'] = $_W['uid'] = 0;
        $_W['user'] = array('uid'=>$_W['uid'],'username'=>'未登录');
        $_W['account'] = array('uniacid'=>0);
        $_W['inconsole'] = $_W['inapp'] = false;
        $_W['token'] = csrf_token();
        SettingService::Load();
        $_W['page'] = $_W['setting']['page'];
        return $next($request);
    }

}
