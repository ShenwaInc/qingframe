<?php

use App\Services\SettingService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CatchCall {

    public $error = '';
    public $errno = -1;
    public function __construct($error, $errno=-1){
        $this->error = $error;
        $this->errno = $errno;
    }

    public function __call($name, $arguments){
        // TODO: Implement __call() method.
        return error($this->errno, $this->error);
    }

}

/**
 * 调用服务方法
 * @param string $name 服务名称
 * @param array|null $params 构造参数
 * @return object 服务实例
 */
function serv(string $name, $params=null){
    static $_servers;
    if (empty($_servers)) $_servers = array();
    if (isset($_servers[$name])){
        return $_servers[$name];
    }
    $service = MICRO_SERVER.strtolower($name).'/'.ucfirst($name)."Service.php";
    if (!file_exists($service)){
        return new CatchCall("Service ".ucfirst($name)." Not Found.");
    }
    require_once $service;
    $class_name = ucfirst($name) . 'Service';
    $instance = new $class_name($params);
    if ($instance->service['status']!=1){
        return new CatchCall("Service $name has stopped.");
    }
    $_servers[$name] = $instance;
    return $instance;
}

if (!function_exists('post_var')){
    function post_var($keys=array(),$datas=array()){
        global $_GPC;
        $data = array();
        $datas = $datas ?: $_GPC;
        foreach ($keys as $key){
            if (isset($datas[$key])){
                $data[$key] = $datas[$key];
            }
        }
        return $data;
    }
}

function script_run($params, $basedir = MICRO_SERVER){
    if (empty($params['content'])) return true;
    $basedir = preg_replace('/\/$/',"", $basedir);
    switch ($params['drive']){
        case 'php':{
            //运行PHP文件
            $php = $basedir."/{$params['content']}";
            if (file_exists($php)){
                include $php;
            }
            break;
        }
        case 'phpscript':{
            //有风险
            eval($params['content']);
            break;
        }
        case 'sql':{
            pdo_query($params['content']);
            break;
        }
        case 'shell':{
            $sh = $basedir."/{$params['content']}";
            shell_exec($sh);
            break;
        }
        case 'shellscript':{
            shell_exec($params['content']);
            break;
        }
        default : break;
    }
    return true;

}

function strexists($string, $find) {
    return \Str::contains($string,$find);
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

function cache_load($key, $unserialize = false, $default=null){
    $cache = Cache::get($key, $unserialize?array():$default);
    if (!empty($cache) && $unserialize){
        return unserialize($cache);
    }
    return $cache;
}

function cache_write($key, $data, $expire = null) {
    if (empty($expire)){
        return Cache::put($key, $data);
    }
    return Cache::put($key, $data, $expire);
}

function cache_read($key, $default=null){
    return Cache::get($key, $default);
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

    $uni_remote_setting = SettingService::uni_load('remote');
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
        $len = min(9, intval($len));
        $len = max(1, $len);
        $start = (int)pow(10,$len-1);
        $stop = (int)pow(10,$len) - 1;
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

function session_exit($print=''){
    if (!empty($print)){
        echo $print;
    }
    session()->save();
    exit();
}

function tablename($table) {
    $prefix = env("DB_PREFIX", 'ims_');
    return "`$prefix$table`";
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

function pdo_getcount($tablename, $condition=array()){
    return DB::table($tablename)->where($condition)->count();
}

function pdo_getcolumn($tablename, $condition, $field) {
    return DB::table($tablename)->where($condition)->value($field);
}

function pdo_truncate($tablename){
    DB::table($tablename)->truncate();
    return true;
}

function pdo_insert($tablename,$data,$insertgetid=false){
    $query = DB::table($tablename);
    if ($insertgetid){
        return $query->insertGetId($data);
    }
    return $query->insert($data);
}

function pdo_insertgetid($tablename,$data){
    return DB::table($tablename)->insertGetId($data);
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
    return DB::statement($sql);
}

function pdo_query($sql, $params = array()) {
    return DB::statement($sql);
}
