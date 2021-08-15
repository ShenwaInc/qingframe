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
        $_W['config'] = config('system');
        $dbconfig = config('database');
        $_W['config']['db'] = $dbconfig['connections'][$dbconfig['default']];
        $_W['timestamp'] = TIMESTAMP;
        $_W['charset'] = $_W['config']['setting']['charset'];
        $_W['clientip'] = $this->GetIp();
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($_W['config']['setting']['timezone']);
        }
        if (!empty($_W['config']['setting']['memory_limit']) && function_exists('ini_get') && function_exists('ini_set')) {
            if ($_W['config']['setting']['memory_limit'] != @ini_get('memory_limit')) {
                @ini_set('memory_limit', $_W['config']['setting']['memory_limit']);
            }
        }
        if (isset($_W['config']['setting']['https']) && $_W['config']['setting']['https'] == '1') {
            $_W['ishttps'] = $_W['config']['setting']['https'];
        } else {
            $_W['ishttps'] = isset($_SERVER['SERVER_PORT']) && 443 == $_SERVER['SERVER_PORT'] ||
            isset($_SERVER['HTTP_FROM_HTTPS']) && 'on' == strtolower($_SERVER['HTTP_FROM_HTTPS']) ||
            (isset($_SERVER['HTTPS']) && 'off' != strtolower($_SERVER['HTTPS'])) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) ||
            isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && 'https' == strtolower($_SERVER['HTTP_X_CLIENT_SCHEME']) 			? true : false;
        }
        $_W['isajax'] = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
        $_W['ispost'] = isset($_SERVER['REQUEST_METHOD']) && 'POST' == $_SERVER['REQUEST_METHOD'];
        $_W['sitescheme'] = $_W['ishttps'] ? 'https://' : 'http://';
        $sitepath = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
        $_W['siteroot'] = htmlspecialchars($_W['sitescheme'] . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $sitepath);
        if ('/' != substr($_W['siteroot'], -1)) {
            $_W['siteroot'] .= '/';
        }
        $urls = parse_url($_W['siteroot']);
        $urls['path'] = str_replace(array('/web', '/app', '/payment/wechat', '/payment/alipay', '/payment/jueqiymf', '/api'), '', $urls['path']);
        $urls['scheme'] = !empty($urls['scheme']) ? $urls['scheme'] : 'http';
        $urls['host'] = !empty($urls['host']) ? $urls['host'] : '';
        $_W['siteroot'] = $urls['scheme'] . '://' . $urls['host'] . ((!empty($urls['port']) && '80' != $urls['port']) ? ':' . $urls['port'] : '') . $urls['path'];
        $_GPC = $request->all();
        $_W['siteurl'] = url()->full();
        $_W['uniacid'] = $_W['uid'] = 0;
        SettingService::Load();
        if ($_W['config']['setting']['development'] == 1 || $_W['setting']['copyright']['develop_status'] ==1) {
            ini_set('display_errors', '1');
            error_reporting(E_ALL ^ E_NOTICE);
        }
        $_W['uid'] = 0;
        $_W['user'] = array('uid'=>0,'username'=>'未登录');
        if (!isset($_W['setting']['page']) || empty($_W['setting']['page'])){
            $_W['page'] = array(
                'title'=>'Whotalk即时通讯系统',
                'icon'=>'/favicon.ico',
                'logo'=>'/static/icon200.jpg',
                'copyright'=>'© 2019-2022 Shenwa Studio. All Rights Reserved.',
                'links'=>'
                <a class="copyright-link" href="https://www.whotalk.com.cn/" target="_blank">Whotalk官网</a>
                <a class="copyright-link" href="https://chat.gxit.org/app/index.php?i=4&c=entry&m=swa_supersale&do=app&r=whotalkcloud.post" target="_blank">制作APP</a>
                <a class="copyright-link" href="https://shimo.im/docs/XRkgJOKZ41UrFbqM" target="_blank">使用教程</a>
                <a class="copyright-link" href="https://www.yuque.com/docs/share/84abf7ef-7d11-44f1-a510-ed70ef14ef3d?#" target="_blank">更新日志</a>
                '
            );
        }else{
            $_W['page'] = $_W['setting']['page'];
        }
        return $next($request);
    }

    public function GetIp(){
        static $ip = '';
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] as $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $ip)) {
            return $ip;
        } else {
            return '127.0.0.1';
        }
    }

}
