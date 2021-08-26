<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */

use App\Services\ModuleService;

defined('IN_IA') or exit('Access Denied');

function template($filename, $flag = 0) {
	global $_W;
	$source = IA_ROOT . "/web/themes/{$_W['template']}/{$filename}.html";
	$compile = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$filename}.tpl.php";
	if (!is_file($source)) {
		$source = IA_ROOT . "/web/themes/default/{$filename}.html";
		$compile = IA_ROOT . "/data/tpl/web/default/{$filename}.tpl.php";
	}

	if (!is_file($source)) {
		echo "template source '{$filename}' is not exist!";

		return '';
	}
	if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
		template_compile($source, $compile);
	}
	switch ($flag) {
		case 0:
		default:
			extract($GLOBALS, EXTR_SKIP);
			include $compile;
			break;
		case 1:
			extract($GLOBALS, EXTR_SKIP);
			ob_flush();
			ob_clean();
			ob_start();
			include $compile;
			$contents = ob_get_contents();
			ob_clean();

			return $contents;
			break;
		case 2:
			return $compile;
			break;
	}
}


function template_compile($from, $to, $inmodule = false) {
	global $_W;
	$path = dirname($to);
	if (!is_dir($path)) {
		\App\Services\FileService::mkdirs($path);
	}
	$content = template_parse(file_get_contents($from), $inmodule);

	file_put_contents($to, $content);
}


function template_parse($str, $inmodule = false) {
	$str = preg_replace('/<!--{(.+?)}-->/s', '{$1}', $str);
	$str = preg_replace('/{template\s+(.+?)}/', '<?php (!empty($this) && $this instanceof WeModule || ' . intval($inmodule) . ') ? (include $this->template($1, 2)) : (include template($1, TEMPLATE_INCLUDEPATH));?>', $str);
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
	$str = preg_replace_callback('/<\?php([^\?]+)\?>/s', 'template_addquote', $str);
	$str = preg_replace_callback('/{hook\s+(.+?)}/s', 'template_modulehook_parser', $str);
	$str = preg_replace('/{\/hook}/', '<?php ; ?>', $str);
	$str = preg_replace('/{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)}/s', '<?php echo $1;?>', $str);
	$str = str_replace('{##', '{', $str);
	$str = str_replace('##}', '}', $str);
	if (!empty($GLOBALS['_W']['setting']['remote']['type'])) {
		$str = str_replace('</body>', "<script>$(function(){\$('img').attr('onerror', '').on('error', function(){if (!\$(this).data('check-src') && (this.src.indexOf('http://') > -1 || this.src.indexOf('https://') > -1)) {this.src = this.src.indexOf('{$GLOBALS['_W']['attachurl_local']}') == -1 ? this.src.replace('{$GLOBALS['_W']['attachurl_remote']}', '{$GLOBALS['_W']['attachurl_local']}') : this.src.replace('{$GLOBALS['_W']['attachurl_local']}', '{$GLOBALS['_W']['attachurl_remote']}');\$(this).data('check-src', true);}});});</script></body>", $str);
	}
	$str = "<?php defined('IN_IA') or exit('Access Denied');?>" . $str;

	return $str;
}

function template_addquote($matchs) {
	$code = "<?php {$matchs[1]}?>";
	$code = preg_replace('/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\](?![a-zA-Z0-9_\-\.\x7f-\xff\[\]]*[\'"])/s', "['$1']", $code);

	return str_replace('\\\"', '\"', $code);
}

function template_modulehook_parser($params = array()) {
	load()->model('module');
	if (empty($params[1])) {
		return '';
	}
	$params = explode(' ', $params[1]);
	if (empty($params)) {
		return '';
	}
	$plugin = array();
	foreach ($params as $row) {
		$row = explode('=', $row);
		$plugin[$row[0]] = str_replace(array("'", '"'), '', $row[1]);
		$row[1] = urldecode($row[1]);
	}
	$plugin_info = ModuleService::fetch($plugin['module']);
	if (empty($plugin_info)) {
		return false;
	}

	if (empty($plugin['return']) || 'false' == $plugin['return']) {
			} else {
			}
	if (empty($plugin['func']) || empty($plugin['module'])) {
		return false;
	}

	if (defined('IN_SYS')) {
		$plugin['func'] = "hookWeb{$plugin['func']}";
	} else {
		$plugin['func'] = "hookMobile{$plugin['func']}";
	}


    return $php;
}

function tpl_ueditor($id, $value = '', $options = array()) {
    global $_W;
    $options['uniacid'] = isset($options['uniacid']) ? intval($options['uniacid']) : $_W['uniacid'];
    $options['global'] = empty($options['global']) ? '' : $options['global'];
    $options['height'] = empty($options['height']) ? 200 : $options['height'];
    $options['allow_upload_video'] = isset($options['allow_upload_video']) ? $options['allow_upload_video'] : true;

    $s = '';
    $s .= !empty($id) ? "<textarea id=\"{$id}\" name=\"{$id}\" type=\"text/plain\" style=\"height:{$options['height']}px;\">{$value}</textarea>" : '';
    $s .= "
	<script type=\"text/javascript\">
		require(['util'], function(util){
			util.editor('" . ($id ? $id : '') . "', {
			uniacid : {$options['uniacid']},
			global : '" . $options['global'] . "',
			height : {$options['height']},
			dest_dir : '" . ($options['dest_dir'] ? $options['dest_dir'] : '') . "',
			image_limit : " . (intval($GLOBALS['_W']['setting']['upload']['image']['limit']) * 1024) . ',
			allow_upload_video : ' . ($options['allow_upload_video'] ? 'true' : 'false') . ',
			audio_limit : ' . (intval($GLOBALS['_W']['setting']['upload']['audio']['limit']) * 1024) . ",
			callback : ''
			});
		});
	</script>";

    return $s;
}

function tpl_form_field_image($name, $value) {
    $thumb = empty($value) ? 'images/global/nopic.jpg' : $value;
    $thumb = tomedia($thumb);
    $html = <<<EOF
<style>
.webuploader-pick {color:#333;}
</style>
<div class="input-group">
	<input type="hidden" name="$name" value="$value" class="form-control" autocomplete="off" readonly="readonly">
	<a class="btn btn-default js-image-{$name}">上传图片</a>
</div>
<span class="help-block">
	<img src="$thumb" >
</span>

<script>
	util.image($('.js-image-{$name}'), function(url){
		$('.js-image-{$name}').prev().val(url.attachment);
		$('.js-image-{$name}').parent().next().find('img').attr('src',url.url);
	}, {
		crop : false,
		multiple : false
	});
</script>
EOF;
    return $html;
}

function tpl_form_field_multi_image($name, $value = array(), $options = array()) {
    global $_W;
    $options['multiple'] = true;
    $options['direct'] = false;
    $options['fileSizeLimit'] = intval($GLOBALS['_W']['setting']['upload']['image']['limit']) * 1024;
    if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
        if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
            exit('图片上传目录错误,只能指定最多两级目录,如: "we7_store","we7_store/d1"');
        }
    }
    $s = '';
    if (!defined('TPL_INIT_MULTI_IMAGE')) {
        $s = '
<script type="text/javascript">
	function uploadMultiImage(elm) {
		var name = $(elm).next().val();
		util.image( "", function(urls){
		    console.log(urls);
			$.each(urls, function(idx, url){
				$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+url.url+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+url.attachment+\'"><em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em></div>\');
			});
		}, ' . json_encode($options) . ');
	}
	function deleteMultiImage(elm){
		$(elm).parent().remove();
	}
</script>';
        define('TPL_INIT_MULTI_IMAGE', true);
    }

    $s .= <<<EOF
<div class="input-group">
	<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传图片" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="uploadMultiImage(this);">选择图片</button>
		<input type="hidden" value="{$name}" />
	</span>
</div>
<div class="input-group multi-img-details">
EOF;
    if (is_array($value) && count($value) > 0) {
        foreach ($value as $row) {
            $s .= '
<div class="multi-item">
	<img src="' . tomedia($row) . '" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
	<input type="hidden" name="' . $name . '[]" value="' . $row . '" >
	<em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em>
</div>';
        }
    }
    $s .= '</div>';

    return $s;
}

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
	<button class="btn btn-default daterange ' . (!empty($time) ? 'daterange-time' : 'daterange-date') . '" type="button"><span class="date-title">' .
        ($value['starttime'] == "" ? "不限时间" : $value['starttime']) . ($value['starttime'] == "" && $value['endtime'] === "" ? "" : (" 至" . $value['endtime'])) . '</span> <i class="fa fa-calendar"></i></button>
	';

    return $s;
}
