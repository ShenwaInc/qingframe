<?php

namespace App\Http\Middleware;

use App\Services\AgentService;
use App\Services\SettingService;
use Closure;

error_reporting(0);
define('IA_ROOT', base_path('public'));
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('ATTACHMENT_ROOT', IA_ROOT . '/attachment/');
define('TIMESTAMP', time());
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
        $deviceType = AgentService::deviceType();
        $_W['os'] = ['unknown','mobile','windows'][$deviceType];
        $_W['config'] = config('system');
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
        $_GPC = $this->InitGPC();
        $_W['siteurl'] = $urls['scheme'] . '://' . $urls['host'] . ((!empty($urls['port']) && '80' != $urls['port']) ? ':' . $urls['port'] : '').$_SERVER["REQUEST_URI"];
        $_W['uniacid'] = $_W['uid'] = 0;
        SettingService::Load();
        if ($_W['config']['setting']['development'] == 1 || $_W['setting']['copyright']['develop_status'] ==1) {
            ini_set('display_errors', '1');
            error_reporting(E_ALL ^ E_NOTICE);
        }
        return $next($request);
    }

    public function InitGPC(){
        $GPC = array();
        if (MAGIC_QUOTES_GPC) {
            $_GET = $this->istripslashes($_GET);
            $_POST = $this->istripslashes($_POST);
        }
        foreach ($_GET as $key => $value) {
            if (is_string($value) && !is_numeric($value)) {
                $value = $this->safe_gpc_string($value);
            }
            $GPC[$key] = $value;
        }
        return array_merge($GPC, $_POST);
    }

    public function safe_gpc_string($value, $default = '') {
        $value = $this->safe_bad_str_replace($value);
        $value = preg_replace('/&((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $value);

        if (empty($value) && $default != $value) {
            $value = $default;
        }

        return $value;
    }

    public function safe_bad_str_replace($string) {
        if (empty($string)) {
            return '';
        }
        $badstr = array("\0", '%00', '%3C', '%3E', '<?', '<%', '<?php', '{php', '{if', '{loop', '../', '%0D%0A');
        $newstr = array('_', '_', '&lt;', '&gt;', '_', '_', '_', '_', '_', '_', '.._', '_');
        $string = str_replace($badstr, $newstr, $string);

        return $string;
    }

    public function istripslashes($var) {
        if (is_array($var)) {
            foreach ($var as $key => $value) {
                $var[stripslashes($key)] = istripslashes($value);
            }
        } else {
            $var = stripslashes($var);
        }

        return $var;
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
