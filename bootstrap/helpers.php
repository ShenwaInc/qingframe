<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function strexists($string, $find) {
    return \Str::contains($string,$find);
}

function uni_setting_load($name = '', $uniacid = 0){
    return \App\Services\SettingService::uni_load($name, $uniacid);
}

function checksubmit($var='_token'){
    global $_GPC,$_W;
    if (!$_W['ispost']) return false;
    if ($_W['inconsole']){
        $headers = request()->header('X-CSRF-TOKEN');
        return !empty($_GPC[$var]) || !empty($headers);
    }elseif (defined('IN_API') && $var=='_token'){
        return true;
    }
    return isset($_GPC[$var]);
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5('' != $key ? $key : $GLOBALS['_W']['config']['setting']['authkey']);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ('DECODE' == $operation ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = 'DECODE' == $operation ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; ++$i) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; ++$i) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; ++$i) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ('DECODE' == $operation) {
        if ((0 == substr($result, 0, 10) || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

function referer() {
    global $_GPC, $_W;
    $_W['referer'] = !empty($_GPC['referer']) ? $_GPC['referer'] : $_SERVER['HTTP_REFERER'];
    $_W['referer'] = '?' == substr($_W['referer'], -1) ? substr($_W['referer'], 0, -1) : $_W['referer'];

    $_W['referer'] = str_replace('&amp;', '&', $_W['referer']);
    $reurl = parse_url($_W['referer']);

    if (!empty($reurl['host']) && !in_array($reurl['host'], array($_SERVER['HTTP_HOST'], 'www.' . $_SERVER['HTTP_HOST'])) && !in_array($_SERVER['HTTP_HOST'], array($reurl['host'], 'www.' . $reurl['host']))) {
        $_W['referer'] = $_W['siteroot'];
    } elseif (empty($reurl['host'])) {
        $_W['referer'] = $_W['siteroot'] . './' . $_W['referer'];
    }

    return strip_tags($_W['referer']);
}

function wurl($segment, $params = array(), $contain_domain = false){
    global $_W;
    $url = 'console';
    if ($contain_domain){
        $url = $_W['siteroot'] . $url;
    }else{
        $url = '/' . $url;
    }
    if (strexists($segment,'.')){
        $segment = str_replace('.','/',$segment);
    }
    if (!empty($segment)){
        $url .= '/' . $segment;
    }
    if (!empty($params)) {
        $queryString = http_build_query($params, '', '&');
        $url .= '?' . $queryString;
    }
    return $url;
}

function murl($segment, $params = array(), $noredirect = true, $addhost = false) {
    global $_W;
    if (strexists($segment,'.')){
        $segment = str_replace('.','/',$segment);
    }
    if (!empty($addhost)) {
        $url = $_W['siteroot'] . "app";
    } else {
        $url = "/app";
    }

    if (!empty($segment)){
        $url .= '/' .$segment;
    }

    if (empty($params)){
        $params = array();
    }
    $params['i'] = $_W['uniacid'];

    $queryString = http_build_query($params, '', '&');
    $url .= '?' . $queryString;
    if (false === $noredirect) {
        $url .= '&wxref=mp.weixin.qq.com#wechat_redirect';
    }

    return $url;
}

function tomedia($src, $local_path = false, $is_cahce = false) {
    global $_W;
    $src = trim($src);
    if (empty($src)) {
        return '';
    }
    if (file_exists(public_path($src))){
        return asset($src);
    }
    if ($is_cahce) {
        $src .= '?v=' . time();
    }

    $t = strtolower($src);
    if (strexists($t, '//mmbiz.qlogo.cn') || strexists($t, '//mmbiz.qpic.cn')) {
        $url = '?a=image&attach='.$src;

        return url('console/util/wxcode') . ltrim($url, '.');
    }

    if (\Str::startsWith($src,'//')) {
        return 'http:' . $src;
    }
    if (\Str::startsWith($src,'http://') || \Str::startsWith($src,'https://')) {
        return $src;
    }

    $uni_remote_setting = \App\Services\SettingService::uni_load('remote');
    if ($local_path || empty($_W['setting']['remote']['type']) && (empty($_W['uniacid']) || !empty($_W['uniacid']) && empty($uni_remote_setting['remote']['type'])) || file_exists(storage_path("app/public/{$src}") )) {
        $src = $_W['siteroot'] . 'storage/' . $src;
    } else {
        if (!empty($uni_remote_setting['remote']['type'])) {
            if (1 == $uni_remote_setting['remote']['type']) {
                $src = $uni_remote_setting['remote']['ftp']['url'] . '/' . $src;
            } elseif (2 == $uni_remote_setting['remote']['type']) {
                $src = $uni_remote_setting['remote']['alioss']['url'] . '/' . $src;
            } elseif (3 == $uni_remote_setting['remote']['type']) {
                $src = $uni_remote_setting['remote']['qiniu']['url'] . '/' . $src;
            } elseif (4 == $uni_remote_setting['remote']['type']) {
                $src = $uni_remote_setting['remote']['cos']['url'] . '/' . $src;
            }

        } else {
            $src = $_W['attachurl_remote'] . $src;
        }
    }

    return $src;
}

function random($len,$is_number=false){
    if($is_number){
        $start = pow(10,$len);
        $stop = pow(10,$len+1) - 1;
        return random_int($start, $stop);
    }
    return \Str::random($len);
}

function is_error($data) {
    if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && 0 == $data['errno'])) {
        return false;
    } else {
        return true;
    }
}

function error($errno, $message = '') {
    return array(
        'errno' => $errno,
        'message' => $message
    );
}

function uni_fetch($uniacid = 0) {
    global $_W;
    $uniacid = empty($uniacid) ? $_W['uniacid'] : intval($uniacid);
    $account_api = \App\Utils\WeAccount::createByUniacid($uniacid);
    if (is_error($account_api)) {
        return $account_api;
    }
    $account_api->uniacid = $uniacid;
    $account_api->__toArray();
    $account_api['accessurl'] = $account_api['manageurl'] = url("console/account/{$uniacid}/post", array('account_type' => $account_api['type']), true);
    $account_api['roleurl'] = url("console/account/{$uniacid}/postuser", array('account_type' => $account_api['type']), true);
    return $account_api;
}

function session_exit($print=''){
    if (!empty($print)){
        echo $print;
    }
    session()->save();
    exit();
}

function tablename($table) {
    return "`{$GLOBALS['_W']['config']['db']['prefix']}{$table}`";
}

function pdo_get($tablename, $condition = array(), $fields = array()) {
    $query = DB::table($tablename)->where($condition);
    if (empty($fields)){
        return $query->first();
    }
    return $query->first($fields);
}

function pdo_getall($tablename, $condition = array(), $fields = array(), $keyfield = '', $orderby = array(), $limit = array()) {
    $query = DB::table($tablename)->where($condition);
    if ($fields){
        $query = $query->select($fields);
    }
    if (!empty($orderby)){
        if (!is_array($orderby)){
            $orderby = array($orderby,'desc');
        }
        $query = $query->orderBy($orderby[0],$orderby[1]);
    }
    if ($limit){
        $query = $query->offset($limit[0])->limit($limit[1]);
    }
    if ($keyfield){
        $res = $query->get()->keyBy($keyfield)->toArray();
    }else{
        $res = $query->get()->toArray();
    }
    return $res;
}

function pdo_fetchall($sql, $params = array()) {
    return DB::select($sql,$params);
}

function pdo_fetchcolumn($sql, $params = array(), $column = 0) {
    $result = DB::selectOne($sql,$params);
    if (!empty($result)){
        $keys = array_keys($result);
        if (isset($result[$keys[$column]])){
            return $result[$keys[$column]];
        }
    }
    return null;
}

function pdo_fetch($sql, $params = array()) {
    return DB::selectOne($sql,$params);
}

function pdo_getcolumn($tablename, $condition, $field) {
    return DB::table($tablename)->where($condition)->value($field);
}

function pdo_insert($tablename,$data,$insertgetid=false){
    $query = DB::table($tablename);
    if ($insertgetid){
        return $query->insertGetId($data);
    }
    return $query->insert($data);
}

function pdo_update($table, $data = array(), $params = array()) {
    return DB::table($table)->where($params)->update($data);
}

function pdo_delete($table, $params = array()) {
    return DB::table($table)->where($params)->delete();
}

function pdo_count($tablename, $condition = array(), $cachetime = 15) {
    return DB::table($tablename)->where($condition)->count();
}

function pdo_fieldexists($tablename, $fieldname = '') {
    return Schema::hasColumn($tablename,$fieldname);
}

function pdo_tableexists($tablename) {
    return Schema::hasTable($tablename);
}

function pdo_run($sql) {
    dd(error(-1,'Function pdo_run() was deprecated. Try Schema::'));
}

function pdo_query($sql, $params = array()) {
    dd(error(-1,'Function pdo_query() was deprecated. Try Schema::'));
}

function array_elements($keys, $src, $default = false) {
    $return = array();
    if (!is_array($keys)) {
        $keys = array($keys);
    }
    foreach ($keys as $key) {
        if (isset($src[$key])) {
            $return[$key] = $src[$key];
        } else {
            $return[$key] = $default;
        }
    }

    return $return;
}

function array2xml($arr, $level = 1) {
    $s = 1 == $level ? '<xml>' : '';
    foreach ($arr as $tagname => $value) {
        if (is_numeric($tagname)) {
            $tagname = $value['TagName'];
            unset($value['TagName']);
        }
        if (!is_array($value)) {
            $s .= "<{$tagname}>" . (!is_numeric($value) ? '<![CDATA[' : '') . $value . (!is_numeric($value) ? ']]>' : '') . "</{$tagname}>";
        } else {
            $s .= "<{$tagname}>" . array2xml($value, $level + 1) . "</{$tagname}>";
        }
    }
    $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);

    return 1 == $level ? $s . '</xml>' : $s;
}

function isimplexml_load_string($string, $class_name = 'SimpleXMLElement', $options = 0, $ns = '', $is_prefix = false) {
    libxml_disable_entity_loader(true);
    if (preg_match('/(\<\!DOCTYPE|\<\!ENTITY)/i', $string)) {
        return false;
    }
    $string = preg_replace('/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f\\x7f]/', '', $string); 	return simplexml_load_string($string, $class_name, $options, $ns, $is_prefix);
}

function utf8_bytes($cp) {
    if ($cp > 0x10000) {
        return	chr(0xF0 | (($cp & 0x1C0000) >> 18)) .
            chr(0x80 | (($cp & 0x3F000) >> 12)) .
            chr(0x80 | (($cp & 0xFC0) >> 6)) .
            chr(0x80 | ($cp & 0x3F));
    } elseif ($cp > 0x800) {
        return	chr(0xE0 | (($cp & 0xF000) >> 12)) .
            chr(0x80 | (($cp & 0xFC0) >> 6)) .
            chr(0x80 | ($cp & 0x3F));
    } elseif ($cp > 0x80) {
        return	chr(0xC0 | (($cp & 0x7C0) >> 6)) .
            chr(0x80 | ($cp & 0x3F));
    } else {
        return chr($cp);
    }
}

function parse_path($path) {
    $danger_char = array('../', '{php', '<?php', '<%', '<?', '..\\', '\\\\', '\\', '..\\\\', '%00', '\0', '\r');
    foreach ($danger_char as $char) {
        if (strexists($path, $char)) {
            return false;
        }
    }

    return $path;
}

function iget_headers($url, $format = 0) {
    $result = @get_headers($url, $format);
    if (empty($result)) {
        stream_context_set_default(array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
            ),
        ));
        $result = get_headers($url, $format);
    }

    return $result;
}

function sizecount($size, $unit = false) {
    if ($size >= 1073741824) {
        $size = round($size / 1073741824 * 100) / 100 . ' GB';
    } elseif ($size >= 1048576) {
        $size = round($size / 1048576 * 100) / 100 . ' MB';
    } elseif ($size >= 1024) {
        $size = round($size / 1024 * 100) / 100 . ' KB';
    } else {
        $size = $size . ' Bytes';
    }
    if ($unit) {
        $size = preg_replace('/[^0-9\.]/', '', $size);
    }

    return $size;
}

function igetimagesize($filename, $imageinfo = array()) {
    $result = @getimagesize($filename, $imageinfo);
    if (empty($result)) {
        $file_content = \App\Services\HttpService::ihttp_request($filename);
        $content = $file_content['content'];
        $result = getimagesize('data://image/jpeg;base64,' . base64_encode($content), $imageinfo);
    }

    return $result;
}
