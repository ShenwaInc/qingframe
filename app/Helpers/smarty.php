<?php
/**
 * HTML模板编译引擎
 *
 * @author 神蛙科技
 * @url
 */

use App\Services\FileService;

function tpl_compile($from, $to) {
    if (file_exists($to)){
        if(filemtime($to)>filemtime($from)) return true;
    }
    $path = dirname($to);
    if (!is_dir($path)) {
        FileService::mkdirs($path);
    }
    $content = tpl_parse(file_get_contents($from));
    return file_put_contents($to, $content);
}

function tpl_token(){
    global $_W;
    return '<input type="hidden" name="_token" value="'.$_W['token'].'" />';
}

function tpl_parse($str) {
    $check_repeat_template = array(
        "'common\\/header'",
        "'common\\/footer'",
    );
    foreach ($check_repeat_template as $template) {
        if (preg_match_all('/{template\s+'.$template.'}/', $str, $match) > 1) {
            $replace = stripslashes($template);
            $str = preg_replace('/{template\s+'.$template.'}/i', '<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('.$replace.', TEMPLATE_INCLUDEPATH)) : (include template('.$replace.', TEMPLATE_INCLUDEPATH));?>', $str, 1);
            $str = preg_replace('/{template\s+'.$template.'}/i', '', $str);
        }
    }
    $str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
    $str = preg_replace('/{template\s+(["\'])+server\/(.+?):(.+?)(["\'])}/', '<?php include tpl_server("$3","$2");?>', $str);
    $str = preg_replace('/{template\s+(.+?)}/', '<?php include tpl_include($1);?>', $str);
    $str = preg_replace('/{php\s+(.+?)}/', '<?php $1?>', $str);
    $str = preg_replace('/{if\s+(.+?)}/', '<?php if($1) { ?>', $str);
    $str = preg_replace('/{else}/', '<?php } else { ?>', $str);
    $str = preg_replace('/{else ?if\s+(.+?)}/', '<?php } else if($1) { ?>', $str);
    $str = preg_replace('/{\/if}/', '<?php } ?>', $str);
    $str = preg_replace('/{loop\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2) { ?>', $str);
    $str = preg_replace('/{loop\s+(\S+)\s+(\S+)\s+(\S+)}/', '<?php if(is_array($1)) { foreach($1 as $2 => $3) { ?>', $str);
    $str = preg_replace('/{\/loop}/', '<?php } } ?>', $str);
    $str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)}/', '<?php echo $1;?>', $str);
    $str = preg_replace('/{(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\[\]\'\"\$]*)}/', '<?php echo $1;?>', $str);
    $str = preg_replace('/{url\s+(\S+)}/', '<?php echo url($1);?>', $str);
    $str = preg_replace('/{url\s+(\S+)\s+(array\(.+?\))}/', '<?php echo url($1, $2);?>', $str);
    $str = preg_replace('/{media\s+(\S+)}/', '<?php echo tomedia($1);?>', $str);
    $str = preg_replace_callback('/{hook\s+(.+?)}/s', "template_modulehook_parser", $str);
    $str = preg_replace('/{\/hook}/', '<?php ; ?>', $str);
    $str = preg_replace_callback('/<\?php([^\?]+)\?>/s', "template_addquote", $str);
    $str = preg_replace('/{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)}/s', '<?php echo $1;?>', $str);
    $str = str_replace('{##', '{', $str);
    $str = str_replace('##}', '}', $str);
    $str = preg_replace('/{csrftoken}/', '<?php echo tpl_token(); ?>', $str);
    $str = preg_replace('/{ajaxhash}/', ' ajaxhash="'.$GLOBALS['_GPC']['ajaxhash'].'"', $str);

    return $str;
}

/**
 * 加载并渲染视图文件
 * @param string|null $template 路由名称
 * @param string|null $server 服务标识
 * @param string 返回编译后的PHP文件路径（绝对路径）
 * @throws Exception
 */
function tpl_server($template="", $server=''){
    global $_W;
    if (empty($server)){
        $server = $_W['server'];
    }
    $platform = defined('IN_SYS') ? 'web' : 'app';
    if(empty($template)){
        $template = tpl_build($_W['controller'], $_W['action'], MICRO_SERVER."$server/template/$platform");
    }
    $source = MICRO_SERVER.$server."/template/$platform/$template.html";
    if (!file_exists($source)){
        throw new Exception("Error: template source '$template' is not exist!");
    }
    $compile = storage_path("framework/tpls/$platform") . "/severs/$server/$template.tpl.php";
    tpl_compile($source, $compile);
    return $compile;
}

/**
 * @throws Exception
 */
function tpl_include($template){
    $platform = defined('IN_SYS') ? '/' : 'mobile/';
    $source = resource_path("template$platform")."$template.html";
    if (!file_exists($source)){
        throw new Exception("Error: template source '$template' is not exist!");
    }
    $compile = storage_path("framework/tpls/$platform").$template.".tpl.php";
    tpl_compile($source, $compile);
    return $compile;
}

function tpl_build($controller='index', $method='main', $basepath=''){
    $template = $controller;
    if ($controller=='index'){
        if ($method!='main'){
            $template = $method;
        }
    }else{
        if ($method=='main'){
            $template .= '/index';
            if (!file_exists("$basepath/$template.html")){
                $template = $controller;
            }
        }else{
            $template .= "/{$method}";
        }
    }
    return $template;
}

/**
 * @throws Exception
 */
function tpl_load($basepath, $controller='index', $method='main', $platform='web'){
    $template = $controller;
    if ($controller=='index'){
        if ($method!='main'){
            $template = $method;
        }
    }else{
        if ($method=='main'){
            $template .= '/index';
            if (!file_exists(MODULE_ROOT."{$basepath}/template/{$platform}/{$template}.html")){
                $template = $controller;
            }
        }else{
            $template .= "/{$method}";
        }
    }
    $source = MODULE_ROOT."{$basepath}/template/{$platform}/{$template}.html";
    if (!file_exists($source)){
        throw new Exception("Error: template source '{$template}' is not exist!");
    }
    $compile = storage_path("framework/tpls/$platform/").MODULE_IDENTIFIE."/".$basepath."/{$template}.tpl.php";
    tpl_compile($source, $compile);
    return $compile;
}
