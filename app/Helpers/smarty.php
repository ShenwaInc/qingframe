<?php

use App\Services\FileService;

function tpl_compile($from, $to, $module="") {
    if (file_exists($to)){
        if(filemtime($to)>filemtime($from)) return true;
    }
    $path = dirname($to);
    if (!is_dir($path)) {
        FileService::mkdirs($path);
    }
    $content = tpl_parse(file_get_contents($from), $module);
    return file_put_contents($to, $content);
}

function tpl_token(){
    global $_W;
    return '<input type="hidden" name="_token" value="'.$_W['token'].'" />';
}

function tpl_parse($str, $module="") {
    $check_repeat_template = array(
        "'common\\/header'",
        "'common\\/footer'",
    );
    foreach ($check_repeat_template as $template) {
        if (preg_match_all('/{template\s+'.$template.'}/', $str, $match) > 1) {
            $replace = stripslashes($template);
            if (!empty($module)){
                $str = preg_replace('/{template\s+'.$template.'}/i', '<?php include $this->template('.$replace.');?>', $str, 1);
            }else{
                $str = preg_replace('/{template\s+'.$template.'}/i', '<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('.$replace.')) : (include tpl_include('.$replace.'));?>', $str, 1);
            }
            $str = preg_replace('/{template\s+'.$template.'}/i', '', $str);
        }
    }
    $str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
    $str = preg_replace('/{template\s+(["\'])+server\/(.+?):(.+?)(["\'])}/', '<?php include tpl_server("$3","$2");?>', $str);
    if (!empty($module)) {
        $str = preg_replace('/{template\s+(.+?)}/', '<?php include $this->template($1);?>', $str);
    }else{
        $str = preg_replace('/{template\s+(.+?)}/', '<?php include tpl_include($1);?>', $str);
    }
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

    if (function_exists('tpl_parse_extra')){
        $str = tpl_parse_extra($str);
    }

    return $str;
}

/**
 * 加载并渲染视图文件
 * @param string|null $template 路由名称
 * @param string|null $server 服务标识
 * @param string 返回编译后的PHP文件路径（绝对路径）
 * @throws \Exception
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
        throw new \Exception("Error: template source '$template' is not exist!");
    }
    $compile = storage_path("framework/tpls/$platform") . "/severs/$server/$template.tpl.php";
    tpl_compile($source, $compile);
    return $compile;
}

/**
 * @throws \Exception
 */
function tpl_include($template){
    $platform = defined('IN_SYS') ? '/' : 'app/';
    $source = resource_path("template$platform")."$template.html";
    if (defined("TPL_BASEPATH")){
        $_source = TPL_BASEPATH."$template.html";
        if (file_exists($_source)){
            $source = $_source;
        }
    }
    if (!file_exists($source)){
        throw new \Exception("Error: template source '$template' is not exist!");
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

if(!function_exists('tpl_form_field_daterange')){
    function tpl_form_field_daterange($name, $value = array(), $time = false, $clear = true) {
        $s = '';

        if (empty($time) && !defined('TPL_INIT_DATERANGE_DATE')) {
            $s = '
<script type="text/javascript">
	require(["daterangepicker"], function(){
		$(function(){
			$(".daterange.daterange-date").each(function(){
				var elm = this;
				$(this).daterangepicker({
					startDate: $(elm).prev().prev().val() || moment("不限", "Y"),
					endDate: $(elm).prev().val() || moment("不限", "Y"),
					format: "YYYY-MM-DD",
					clear: '. $clear .'
				}, function(start, end){
					start = start.toDateStr().indexOf("0000-01-01") != -1 ? "" : start.toDateStr();
					end = end.toDateStr().indexOf("0000-01-01") != -1 ? "" : end.toDateStr();
					var html = (start == "" ? "不限时间" : start) + (start == "" && end === "" ? "" : (" 至" + end))
					$(elm).find(".date-title").html(html);
					$(elm).prev().prev().val(start);
					$(elm).prev().val(end);
				});
			});
		});
	});
</script>
';
            define('TPL_INIT_DATERANGE_DATE', true);
        }

        if (!empty($time) && !defined('TPL_INIT_DATERANGE_TIME')) {
            $s = '
<script type="text/javascript">
	require(["daterangepicker"], function(){
		$(function(){
			$(".daterange.daterange-time").each(function(){
				var elm = this;
				$(this).daterangepicker({
					startDate: $(elm).prev().prev().val() || moment("不限", "Y"),
					endDate: $(elm).prev().val() || moment("不限", "Y"),
					format: "YYYY-MM-DD HH:mm",
					timePicker: true,
					timePicker12Hour : false,
					timePickerIncrement: 1,
					minuteStep: 1,
					clear: '. $clear .'
				}, function(start, end){
					start = start.toDateStr().indexOf("0000-01-01") != -1 ? "" : start.toDateTimeStr();
					end = end.toDateStr().indexOf("0000-01-01") != -1 ? "" : end.toDateTimeStr();
					var html = (start == "" ? "不限时间" : start) + (start == "" && end === "" ? "" : (" 至" + end))
					$(elm).find(".date-title").html(html);
					$(elm).prev().prev().val(start);
					$(elm).prev().val(end);
				});
			});
		});
	});
</script>
';
            define('TPL_INIT_DATERANGE_TIME', true);
        }
        if (!empty($value['starttime']) || !empty($value['start'])) {
            if ($value['start'] && strtotime($value['start'])) {
                $value['starttime'] = empty($time) ? date('Y-m-d', strtotime($value['start'])) : date('Y-m-d H:i', strtotime($value['start']));
            }
            $value['starttime'] = empty($value['starttime']) ? '' : $value['starttime'];
        } else {
            $value['starttime'] = '';
        }

        if (!empty($value['endtime']) || !empty($value['end'])) {
            if ($value['end'] && strtotime($value['end'])) {
                $value['endtime'] = empty($time) ? date('Y-m-d', strtotime($value['end'])) : date('Y-m-d H:i', strtotime($value['end']));
            }
            $value['endtime'] = empty($value['endtime']) ? $value['starttime'] : $value['endtime'];
        } else {
            $value['endtime'] = '';
        }
        $s .= '
	<input name="' . $name . '[start]' . '" type="hidden" value="' . $value['starttime'] . '" />
	<input name="' . $name . '[end]' . '" type="hidden" value="' . $value['endtime'] . '" />
	<button class="layui-btn layui-btn-primary daterange ' . (!empty($time) ? 'daterange-time' : 'daterange-date') . '" type="button"><span class="date-title">' .
            ($value['starttime'] == "" ? "不限时间" : $value['starttime']) . ($value['starttime'] == "" && $value['endtime'] === "" ? "" : (" 至" . $value['endtime'])) . '</span> <i class="fa fa-calendar"></i></button>
	';

        return $s;
    }
}
