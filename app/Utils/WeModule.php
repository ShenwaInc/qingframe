<?php

namespace App\Utils;

use App\Services\CacheService;
use App\Services\ModuleService;
use Illuminate\Support\Facades\DB;

!(defined('IN_IA')) && define('IN_IA', 2);

class WeModule
{

    public $module;

    public $modulename;

    public $weid;

    public $uniacid;

    public $__define;

    public function create($name){
        global $_W;
        static $file;
        $classname = "{$name}ModuleSite";
        if (!class_exists($classname)) {
            $file = public_path("/addons/{$name}/site.php");
            if (!is_file($file)) {
                trigger_error('ModuleSite Definition File Not Found ' . $file, E_USER_WARNING);
            }
            require $file;
        }
        if (!class_exists($classname)) {
            list($namespace) = explode('_', $name);
            if (class_exists("\\{$namespace}\\{$classname}")) {
                $classname = "\\{$namespace}\\{$classname}";
            } else {
                trigger_error('ModuleSite Definition Class Not Found', E_USER_WARNING);
                return null;
            }
        }
        $o = new $classname();
        $o->uniacid = $o->weid = $_W['uniacid'];
        $o->modulename = $name;
        $o->module = ModuleService::fetch($name);
        $o->__define = $file;
        self::defineConst($o);
        $o->inMobile = defined('IN_MOBILE');
        return $o;
    }

    private static function defineConst($obj) {
        global $_W;

        if (!defined('MODULE_ROOT')) {
            define('MODULE_ROOT', dirname($obj->__define));
        }
        if (!defined('MODULE_URL')) {
            define('MODULE_URL', $_W['siteroot'] . 'addons/' . $obj->modulename . '/');
        }
    }

    public function saveSettings($settings) {
        global $_W;
        $pars = array('module' => $this->modulename, 'uniacid' => $_W['uniacid']);
        $row = array();
        $row['settings'] = serialize($settings);
        $module = DB::table('uni_account_modules')->where(array('module'=>$this->modulename,'uniacid'=>$_W['uniacid']))->value('module');
        if ($module) {
            $result = pdo_update('uni_account_modules', $row, $pars);
        } else {
            $result = pdo_insert('uni_account_modules', array('settings' => serialize($settings), 'module' => $this->modulename, 'uniacid' => $_W['uniacid'], 'enabled' => 1));
        }
        CacheService::build_module($this->modulename, $_W['uniacid']);

        return $result;
    }


    protected function createMobileUrl($do, $query = array(), $noredirect = true, $addhost=false) {
        global $_W;
        $module_name = strtolower($this->modulename);
        $segment = "wem/{$module_name}".($do?'/'.$do:'');
        $url = (!empty($addhost) ? $_W['siteroot'] : '/') . $segment;
        if (empty($query)){
            $query = array();
        }
        $query['i'] = $_W['uniacid'];
        $queryString = http_build_query($query);
        $url .= '?' . $queryString;
        if (false === $noredirect) {
            $url .= '&wxref=mp.weixin.qq.com#wechat_redirect';
        }
        return $url;
    }


    protected function createWebUrl($do, $query = array()) {
        $module_name = strtolower($this->modulename);
        return wurl("m/{$module_name}".($do?'/'.$do:''), $query);
    }


    protected function template($filename, $extra='') {
        global $_W;
        $name = strtolower($this->modulename);
        $defineDir = dirname($this->__define);
        serv("weengine")->func("template");
        if (defined('IN_SYS')) {
            $source = IA_ROOT . "/web/themes/{$_W['template']}/{$name}/{$extra}{$filename}.html";
            $compile = storage_path("framework/tpls/web/{$name}/{$extra}{$filename}.tpl.php");
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$name}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = $defineDir . "/{$extra}template/{$filename}.html";
            }
            if (!is_file($source) && !empty($extra)) {
                $source = $defineDir . "/template/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/{$_W['template']}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/web/themes/default/{$filename}.html";
            }
        } else {
            $source = IA_ROOT . "/app/themes/{$_W['template']}/{$name}/{$filename}.html";
            $compile = storage_path("framework/tpls/app/{$_W['template']}/{$name}/{$filename}.tpl.php");
            if (!is_file($source)) {
                $source = IA_ROOT . "/app/themes/default/{$name}/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = $defineDir . "/template/mobile/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = $defineDir . "/template/wxapp/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = $defineDir . "/template/webapp/{$filename}.html";
            }
            if (!is_file($source)) {
                $source = IA_ROOT . "/app/themes/{$_W['template']}/{$filename}.html";
            }
            if (!is_file($source)) {
                if (in_array($filename, array('header', 'footer', 'slide', 'toolbar', 'message'))) {
                    $source = IA_ROOT . "/app/themes/default/common/{$filename}.html";
                } else {
                    $source = IA_ROOT . "/app/themes/default/{$filename}.html";
                }
            }
        }

        if (!is_file($source)) {
            session()->save();
            exit("Error: template source '{$filename}' is not exist!");
        }
        $paths = pathinfo($compile);
        $compile = str_replace($paths['filename'], $_W['uniacid'] . '_' . $paths['filename'], $compile);
        if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
            template_compile($source, $compile, true);
        }

        return $compile;
    }


    protected function fileSave($file_string, $type = 'jpg', $name = 'auto') {
        global $_W;
        load()->func('file');

        $allow_ext = array(
            'images' => array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'ico'),
            'audios' => array('mp3', 'wma', 'wav', 'amr'),
            'videos' => array('wmv', 'avi', 'mpg', 'mpeg', 'mp4'),
        );
        if (in_array($type, $allow_ext['images'])) {
            $type_path = 'images';
        } elseif (in_array($type, $allow_ext['audios'])) {
            $type_path = 'audios';
        } elseif (in_array($type, $allow_ext['videos'])) {
            $type_path = 'videos';
        }

        if (empty($type_path)) {
            return error(1, '禁止保存文件类型');
        }

        $uniacid = intval($_W['uniacid']);
        if (empty($name) || 'auto' == $name) {
            $path = "{$type_path}/{$uniacid}/{$this->module['name']}/" . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);

            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $type);
        } else {
            $path = "{$type_path}/{$uniacid}/{$this->module['name']}/";
            mkdirs(dirname(ATTACHMENT_ROOT . '/' . $path));

            $filename = $name;
            if (!strexists($filename, $type)) {
                $filename .= '.' . $type;
            }
        }
        if (file_put_contents(ATTACHMENT_ROOT . $path . $filename, $file_string)) {
            file_remote_upload($path);

            return $path . $filename;
        } else {
            return false;
        }
    }

    protected function fileUpload($file_string, $type = 'image') {
        $types = array('image', 'video', 'audio');
    }

    protected function getFunctionFile($name) {
        $module_type = str_replace('wemodule', '', strtolower(get_parent_class($this)));
        if ('site' == $module_type) {
            $module_type = 0 === stripos($name, 'doWeb') ? 'web' : 'mobile';
            $function_name = 'web' == $module_type ? strtolower(substr($name, 5)) : strtolower(substr($name, 8));
        } else {
            $function_name = strtolower(substr($name, 6));
        }
        $dir = IA_ROOT . '/framework/builtin/' . $this->modulename . '/inc/' . $module_type;
        $file = "$dir/{$function_name}.inc.php";
        if (!file_exists($file)) {
            $file = str_replace('framework/builtin', 'addons', $file);
        }

        return $file;
    }

    public function __call($name, $param) {
        $file = $this->getFunctionFile($name);
        if (file_exists($file)) {
            require $file;
            exit;
        }
        trigger_error('模块方法' . $name . '不存在.', E_USER_WARNING);

        return false;
    }

}
