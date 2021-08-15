<?php

function strexists($string, $find) {
    return \Str::contains($string,$find);
}

function uni_setting_load($name = '', $uniacid = 0){
    return \App\Services\SettingService::uni_load($name, $uniacid);
}

function tomedia($src, $local_path = false, $is_cahce = false) {
    global $_W;
    $src = trim($src);
    if (empty($src)) {
        return '';
    }
    if ($is_cahce) {
        $src .= '?v=' . time();
    }

    if (strexists($src, 'c=utility&a=wxcode&do=image&attach=')) {
        return $src;
    }

    $t = strtolower($src);
    if (strexists($t, '//mmbiz.qlogo.cn') || strexists($t, '//mmbiz.qpic.cn')) {
        $url = url('utility/wxcode/image', array('attach' => $src));

        return $_W['siteroot'] . 'web' . ltrim($url, '.');
    }

    if ('//' == substr($src, 0, 2)) {
        return 'http:' . $src;
    }
    if (('http://' == substr($src, 0, 7)) || ('https://' == substr($src, 0, 8))) {
        return $src;
    }

    if (strexists($src, 'addons/')) {
        return $_W['siteroot'] . substr($src, strpos($src, 'addons/'));
    }
    if (strexists($src, 'app/themes/')) {
        return $_W['siteroot'] . substr($src, strpos($src, 'app/themes/'));
    }
    if (strexists($src, $_W['siteroot']) && !strexists($src, '/addons/')) {
        $urls = parse_url($src);
        $src = $t = substr($urls['path'], strpos($urls['path'], 'images'));
    }
    $uni_remote_setting = uni_setting_load('remote');
    if ($local_path || empty($_W['setting']['remote']['type']) && (empty($_W['uniacid']) || !empty($_W['uniacid']) && empty($uni_remote_setting['remote']['type'])) || file_exists(IA_ROOT . '/' . $_W['config']['upload']['attachdir'] . '/' . $src)) {
        $src = $_W['siteroot'] . $_W['config']['upload']['attachdir'] . '/' . $src;
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
    $account_api = \App\Services\AccountService::createByUniacid($uniacid);
    if (is_error($account_api)) {
        return $account_api;
    }
    $account_api->__toArray();
    $account_api['accessurl'] = $account_api['manageurl'] = url("console/account/{$uniacid}/post", array('account_type' => $account_api['type']), true);
    $account_api['roleurl'] = url("console/account/{$uniacid}/postuser", array('account_type' => $account_api['type']), true);
    return $account_api;
}

if (!function_exists('getglobal')) {
    function getglobal($key) {
        global $_W;
        $key = explode('/', $key);
        $v = &$_W;
        foreach ($key as $k) {
            if (!isset($v[$k])) {
                return null;
            }
            $v = &$v[$k];
        }
        return $v;
    }
}

function tablename($table) {
    if (empty($GLOBALS['_W']['config']['db']['master'])) {
        return "`{$GLOBALS['_W']['config']['db']['prefix']}{$table}`";
    }

    return "`{$GLOBALS['_W']['config']['db']['prefix']}{$table}`";
}

function pdo_get($tablename, $condition = array(), $fields = null) {
    $query = \Illuminate\Support\Facades\DB::table($tablename)->where($condition);
    if (empty($fields)){
        return $query->first();
    }
    return $query->first($fields);
}

function pdo_getall($tablename, $condition = array(), $fields = array(), $keyfield = '', $orderby = array(), $limit = array()) {
    $query = \Illuminate\Support\Facades\DB::table($tablename)->where($condition);
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
    return \Illuminate\Support\Facades\DB::select($sql,$params);
}

function pdo_fetchcolumn($sql, $params = array(), $column = 0) {
    $result = \Illuminate\Support\Facades\DB::selectOne($sql,$params);
    if (!empty($result)){
        $keys = array_keys($result);
        if (isset($result[$keys[$column]])){
            return $result[$keys[$column]];
        }
    }
    return null;
}

function pdo_fetch($sql, $params = array()) {
    return \Illuminate\Support\Facades\DB::selectOne($sql,$params);
}

function pdo_getcolumn($tablename, $condition, $field) {
    return \Illuminate\Support\Facades\DB::table($tablename)->where($condition)->value($field);
}

function pdo_insert($tablename,$data,$insertgetid=false){
    $query = \Illuminate\Support\Facades\DB::table($tablename);
    if ($insertgetid){
        return $query->insertGetId($data);
    }
    return $query->insert($data);
}

function pdo_update($table, $data = array(), $params = array()) {
    return \Illuminate\Support\Facades\DB::table($table)->where($params)->update($data);
}

function pdo_delete($table, $params = array()) {
    return \Illuminate\Support\Facades\DB::table($table)->where($params)->delete();
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
