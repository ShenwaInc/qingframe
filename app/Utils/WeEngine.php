<?php

namespace App\Utils;

use App\Services\CacheService;
use App\Services\HttpService;
use App\Services\ModuleService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeEngine
{
    private $account = null;

    private $modules = array();

    public $keyword = array();

    public $message = array();

    public function __construct() {
        global $_W;
        $this->account = WeAccount::create($_W['account']);
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $_W['modules'] = ModuleService::UniModules($_W['account']['uniacid']);
            $this->modules = array_keys($_W['modules']);
            $this->modules[] = 'cover';
            $this->modules[] = 'default';
            $this->modules[] = 'reply';
            $this->modules = array_unique($this->modules);
        }
    }

    public function encrypt() {
        global $_W;
        if(empty($this->account)) {
            exit('Miss Account.');
        }
        $timestamp = TIMESTAMP;
        $nonce = random(5);
        $token = $_W['account']['token'];
        $signkey = array($token, TIMESTAMP, $nonce);
        sort($signkey, SORT_STRING);
        $signString = implode($signkey);
        $signString = sha1($signString);

        $_GET['timestamp'] = $timestamp;
        $_GET['nonce'] = $nonce;
        $_GET['signature'] = $signString;
        $postStr = file_get_contents('php://input');
        if(!empty($_W['account']['encodingaeskey']) && strlen($_W['account']['encodingaeskey']) == 43 && !empty($_W['account']['key']) && $_W['setting']['development'] != 1) {
            $data = $this->account->encryptMsg($postStr);
            $array = array('encrypt_type' => 'aes', 'timestamp' => $timestamp, 'nonce' => $nonce, 'signature' => $signString, 'msg_signature' => $data[0], 'msg' => $data[1]);
        } else {
            $data = array('', '');
            $array = array('encrypt_type' => '', 'timestamp' => $timestamp, 'nonce' => $nonce, 'signature' => $signString, 'msg_signature' => $data[0], 'msg' => $data[1]);
        }
        exit(json_encode($array));
    }

    public function decrypt() {
        global $_W;
        if(empty($this->account)) {
            exit('Miss Account.');
        }
        $postStr = file_get_contents('php://input');
        if(!empty($_W['account']['encodingaeskey']) && strlen($_W['account']['encodingaeskey']) == 43 && !empty($_W['account']['key']) && $_W['setting']['development'] != 1) {
            $resp = $this->account->local_decryptMsg($postStr);
        } else {
            $resp = $postStr;
        }
        exit($resp);
    }

    public function start() {
        global $_W;
        if(empty($this->account)) {
            exit('Miss Account.');
        }
        if(!$this->account->checkSign()) {
            exit('Check Sign Fail.');
        }
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
            $row = array();
            $row['isconnect'] = 1;
            pdo_update('account', $row, array('uniacid' => $_W['uniacid']));
            $cachekey = CacheService::system_key('uniaccount', array('uniacid' => $_W['uniacid']));
            Cache::forget($cachekey);
            exit(htmlspecialchars($_GET['echostr']));
        }
        if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $postStr = file_get_contents('php://input');
            if(!empty($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
                $postStr = $this->account->decryptMsg($postStr);
            }
            Log::info('trace',$postStr);
            $message = $this->account->parse($postStr);

            $this->message = $message;
            if(empty($message)) {
                Log::alert('waring',error(-1,'Request Failed'));
                exit('Request Failed');
            }
            $_W['openid'] = $message['from'];
            $_W['fans'] = array('from_user' => $_W['openid']);
            $this->booking($message);
            if($message['event'] == 'unsubscribe') {
                $this->receive(array(), array(), array());
                exit();
            }
            $sessionid = md5($message['from'] . $message['to'] . $_W['uniacid']);

            session()->put('openid', $_W['openid']);
            $pars = $this->analyze($message);
            $pars[] = array(
                'message' => $message,
                'module' => 'default',
                'rule' => '-1',
            );
            $hitParam['rule'] = -2;
            $hitParam['module'] = '';
            $hitParam['message'] = $message;

            $hitKeyword = array();
            $response = array();
            foreach($pars as $par) {
                if(empty($par['module'])) {
                    continue;
                }
                $par['message'] = $message;
                $response = $this->process($par);
                if($this->isValidResponse($response)) {
                    $hitParam = $par;
                    if(!empty($par['keyword'])) {
                        $hitKeyword = $par['keyword'];
                    }
                    break;
                }
            }
            $response_debug = $response;
            $pars_debug = $pars;
            if($hitParam['module'] == 'default' && is_array($response) && is_array($response['params'])) {
                foreach($response['params'] as $par) {
                    if(empty($par['module'])) {
                        continue;
                    }
                    $response = $this->process($par);
                    if($this->isValidResponse($response)) {
                        $hitParam = $par;
                        if(!empty($par['keyword'])) {
                            $hitKeyword = $par['keyword'];
                        }
                        break;
                    }
                }
            }
            Log::info('params',$hitParam);
            Log::info('response',$response);
            $resp = $this->account->response($response);
            if (!empty($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
                $resp = $this->account->encryptMsg($resp);
                $resp = $this->account->xmlDetract($resp);
            }
            if ($_W['debug']) {
                $_W['debug_data'] = array(
                    'resp' => $resp,
                    'is_default' => 0
                );
                if (count($pars_debug) == 1) {
                    $_W['debug_data']['is_default'] = 1;
                    $_W['debug_data']['params'] = $response_debug['params'];
                } else {
                    array_pop($pars_debug);
                    $_W['debug_data']['params'] = $pars_debug;
                }
                $_W['debug_data']['hitparam'] = $hitParam;
                $_W['modules']['cover'] = array('title' => '入口封面', 'name' => 'cover');

                dd($resp);
            }
            if ($resp !== 'success') {
                $mapping = array(
                    '[from]' => $this->message['from'],
                    '[to]' => $this->message['to'],
                    '[rule]' => $this->params['rule']
                );
                $resp = str_replace(array_keys($mapping), array_values($mapping), $resp);
            }

            $reply_times_info = (array)$_SESSION['__reply_times'];
            if ($reply_times_info['content'] == $message['content']) {
                $new_times = intval($reply_times_info['times']) + 1;
            } else {
                $new_times = 1;
            }
            $_SESSION['__reply_times'] = array('content' => $message['content'], 'date' => date('Y-m-d'), 'times' => $new_times);
            ob_start();
            echo $resp;
            ob_start();
            $this->receive($hitParam, $hitKeyword, $response);
            ob_end_clean();
            exit();
        }
        Log::error('waring',error(-1,'Request Failed'));
        exit('Request Failed');
    }

    private function isValidResponse($response) {
        if ($response === 'success') {
            return true;
        }
        if(is_array($response)) {
            if($response['type'] == 'text' && !empty($response['content'])) {
                return true;
            }
            if($response['type'] == 'news' && !empty($response['items'])) {
                return true;
            }
            if(!in_array($response['type'], array('text', 'news', 'image'))) {
                return true;
            }
        }
        return false;
    }

    private function booking($message) {
        global $_W;
        if ($message['event'] == 'unsubscribe' || $message['event'] == 'subscribe') {
            $todaystat = pdo_get('stat_fans', array('date' => date('Ymd'), 'uniacid' => $_W['uniacid']));
            if ($message['event'] == 'unsubscribe') {
                if (empty($todaystat)) {
                    $updatestat = array(
                        'new' => 0,
                        'uniacid' => $_W['uniacid'],
                        'cancel' => 1,
                        'cumulate' => 0,
                        'date' => date('Ymd'),
                    );
                    pdo_insert('stat_fans', $updatestat);
                } else {
                    $updatestat = array(
                        'cancel' => $todaystat['cancel'] + 1,
                    );
                    pdo_update('stat_fans', $updatestat, array('id' => $todaystat['id']));
                }
            } elseif ($message['event'] == 'subscribe') {
                if (empty($todaystat)) {
                    $updatestat = array(
                        'new' => 1,
                        'uniacid' => $_W['uniacid'],
                        'cancel' => 0,
                        'cumulate' => 0,
                        'date' => date('Ymd'),
                    );
                    pdo_insert('stat_fans', $updatestat);
                } else {
                    $updatestat = array(
                        'new' => $todaystat['new'] + 1,
                    );
                    pdo_update('stat_fans', $updatestat, array('id' => $todaystat['id']));
                }
            }
        }

        $setting = SettingService::uni_load('passport',$_W['uniacid']);
        $fans = mc_fansinfo($message['from']);
        $cachekey = CacheService::system_key('defaultgroupid', array('uniacid' => $_W['uniacid']));
        $default_groupid = Cache::get($cachekey);
        if (empty($default_groupid)) {
            $default_groupid = DB::table('mc_groups')->where(array('uniacid'=>$_W['uniacid'],'isdefault'=>1))->value('groupid');
            Cache::put(CacheService::system_key('defaultgroupid', array('uniacid' => $_W['uniacid'])),$default_groupid,7*86400);
        }
        if(!empty($fans)) {
            if ($message['event'] == 'unsubscribe') {
                CacheService::build_member($fans['uid']);
                pdo_update('mc_mapping_fans', array('follow' => 0, 'unfollowtime' => TIMESTAMP), array('fanid' => $fans['fanid']));
                pdo_delete('mc_fans_tag_mapping', array('fanid' => $fans['fanid']));
            } elseif ($message['event'] != 'ShakearoundUserShake' && $message['type'] != 'trace') {
                $rec = array();
                if (empty($fans['follow'])) {
                    $rec['follow'] = 1;
                    $rec['followtime'] = $message['time'];
                }
                $member = array();
                if(!empty($fans['uid'])){
                    $member = mc_fetch($fans['uid']);
                }
                if (empty($member)) {
                    if (!isset($setting['passport']) || empty($setting['passport']['focusreg'])) {
                        $data = array(
                            'uniacid' => $_W['uniacid'],
                            'email' => md5($message['from']).'@we7.cc',
                            'salt' => random(8),
                            'groupid' => $default_groupid,
                            'createtime' => TIMESTAMP,
                        );
                        $data['password'] = md5($message['from'] . $data['salt'] . $_W['config']['setting']['authkey']);
                        $rec['uid'] = pdo_insert('mc_members', $data, true);
                    }
                }
                if(!empty($rec)){
                    pdo_update('mc_mapping_fans', $rec, array('openid' => $message['from']));
                }
            }
        } else {
            if ($message['event'] == 'subscribe' || $message['type'] == 'text' || $message['type'] == 'image') {
                load()->model('mc');
                $force_init_member = false;
                if (!isset($setting['passport']) || empty($setting['passport']['focusreg'])) {
                    $force_init_member = true;
                }
                mc_init_fans_info($message['from'], $force_init_member);
            }
        }
    }

    private function receive($par, $keyword, $response) {
        global $_W;
        fastcgi_finish_request();


        $subscribe = Cache::get(CacheService::system_key('module_receive_enable'));
        if (empty($subscribe)) {
            $subscribe = CacheService::build_module_subscribe();
        }
        $modules = ModuleService::UniModules($_W['uniacid']);
        if ((empty($subscribe[$this->message['type']]) || $this->message['type'] == 'event') && !empty($this->message['event'])) {
            $subscribe[$this->message['type']] = $subscribe[strtolower($this->message['event'])];
        }
        if (!empty($subscribe[$this->message['type']])) {
            foreach ($subscribe[$this->message['type']] as $modulename) {
                if (!in_array($modulename, array_keys($modules))) {
                    continue;
                }
                $params = array(
                    'i' => $_W['uniacid'],
                    'modulename' => $modulename,
                    'request' => json_encode($par),
                    'response' => json_encode($response),
                    'message' => json_encode($this->message),
                );
                $response = HttpService::ihttp_request(url('wem/subscribe/receive'), $params, array(), 10);
                if (is_error($response) || $response['code'] != 200) {
                    $response = HttpService::ihttp_request($_W['siteroot'] . 'web/' . wurl('utility/subscribe/receive'), $params, array(), 10);
                }
            }
        }
    }

    private function analyze(&$message) {
        global $_W;
        $params = array();
        if(in_array($message['type'], array('event', 'qr'))) {
            $params = call_user_func_array(array($this, 'analyze' . $message['type']), array(&$message));
            if(!empty($params)) {
                return (array)$params;
            }
        }
        if(!empty($_SESSION['__contextmodule']) && in_array($_SESSION['__contextmodule'], $this->modules)) {
            if($_SESSION['__contextexpire'] > TIMESTAMP) {
                $params[] = array(
                    'message' => $message,
                    'module' => $_SESSION['__contextmodule'],
                    'rule' => $_SESSION['__contextrule'],
                    'priority' => $_SESSION['__contextpriority'],
                    'context' => true
                );
                return $params;
            } else {
                unset($_SESSION);
                session_destroy();
            }
        }

        $reply_times_info = (array)$_SESSION['__reply_times'];
        if (!empty($_W['account']['setting']) && !empty($reply_times_info) && intval($_W['account']['setting']['reply_setting']) > 0 && strtotime($reply_times_info['date']) >= strtotime(date('Y-m-d')) && $reply_times_info['times'] >= $_W['account']['setting']['reply_setting'] && $reply_times_info['content'] == $message['content']) {
            exit('success');
        }

        if(method_exists($this, 'analyze' . $message['type'])) {
            $temp = call_user_func_array(array($this, 'analyze' . $message['type']), array(&$message));
            if(!empty($temp) && is_array($temp)){
                $params += $temp;
            }
        } else {
            $params += $this->handler($message['type']);
        }
        return $params;
    }

    private function analyzeSubscribe(&$message) {
        global $_W;
        $params = array();
        $message['type'] = 'text';
        $message['redirection'] = true;
        if(!empty($message['scene'])) {
            $message['source'] = 'qr';
            $sceneid = trim($message['scene']);
            if (is_numeric($sceneid)) {
                $scene_condition = " `qrcid` = :sceneid";
            }else{
                $scene_condition = " `scene_str` = :sceneid";
            }
            $condition = array(':sceneid' => $sceneid, ':uniacid' => $_W['uniacid']);
            $qr = pdo_fetch("SELECT `id`, `keyword` FROM " . tablename('qrcode') . " WHERE {$scene_condition} AND `uniacid` = :uniacid", $condition);
            if(!empty($qr)) {
                $message['content'] = $qr['keyword'];
                if (!empty($qr['type']) && $qr['type'] == 'scene') {
                    $message['msgtype'] = 'text';
                }
                $params += $this->analyzeText($message);
                return $params;
            }
        }
        $message['source'] = 'subscribe';
        $setting = uni_setting($_W['uniacid'], array('welcome'));
        if(!empty($setting['welcome'])) {
            $message['content'] = $setting['welcome'];
            $params += $this->analyzeText($message);
        }

        return $params;
    }

    private function analyzeQR(&$message) {
        global $_W;
        $params = array();
        $default_message = $message;
        $message['type'] = 'text';
        $message['redirection'] = true;
        if(!empty($message['scene'])) {
            $message['source'] = 'qr';
            $sceneid = trim($message['scene']);
            if (is_numeric($sceneid)) {
                $scene_condition = " `qrcid` = :sceneid";
            }else{
                $scene_condition = " `scene_str` = :sceneid";
            }
            $condition_params = array(':sceneid' => $sceneid, ':uniacid' => $_W['uniacid']);
            $qr = pdo_fetch("SELECT `id`, `keyword` FROM " . tablename('qrcode') . " WHERE {$scene_condition} AND `uniacid` = :uniacid AND `type` = 'scene'", $condition_params);

        }
        if (empty($qr) && !empty($message['ticket'])) {
            $message['source'] = 'qr';
            $ticket = trim($message['ticket']);
            if(!empty($ticket)) {
                $qr = pdo_fetchall("SELECT `id`, `keyword` FROM " . tablename('qrcode') . " WHERE `uniacid` = :uniacid AND ticket = :ticket", array(':uniacid' => $_W['uniacid'], ':ticket' => $ticket));
                if(!empty($qr)) {
                    if(count($qr) != 1) {
                        $qr = array();
                    } else {
                        $qr = $qr[0];
                    }
                }
            }
        }
        if(!empty($qr)) {
            $message['content'] = $qr['keyword'];
            if (!empty($qr['type']) && $qr['type'] == 'scene') {
                $message['msgtype'] = 'text';
            }
            $params += $this->analyzeText($message);
        }
        if (empty($qr)) {
            $params = $this->handler($default_message['type']);
            if (!empty($params)) {
                $message = $default_message;
                return $params;
            }
        }
        if (empty($params)) {
            $params = $this->handler($message['type']);
        }
        return $params;
    }

    public function analyzeText(&$message, $order = 0) {
        global $_W;

        $pars = array();

        $order = intval($order);
        if(!isset($message['content'])) {
            return $pars;
        }
        $cachekey = CacheService::system_key('keyword', array('content' => md5($message['content']), 'uniacid' => $_W['uniacid']));
        $keyword_cache = Cache::get($cachekey);
        if (!empty($keyword_cache) && $keyword_cache['expire'] > TIMESTAMP) {
            foreach ($keyword_cache['data'] as $key => &$value) {
                $value['message'] = $message;
            }
            unset($value);
            return $keyword_cache['data'];
        }
        $condition = <<<EOF
`uniacid` IN ( 0, {$_W['uniacid']} )
AND
(
	( `type` = 1 AND `content` = :c1 )
	or
	( `type` = 2 AND instr(:c2, `content`) )
	or
	( `type` = 3 AND :c3 REGEXP `content`)
	or
	( `type` = 4 )
)
AND `status`=1
EOF;

        $params = array();
        $params[':c1'] = $message['content'];
        $params[':c2'] = $message['content'];
        $params[':c3'] = $message['content'];

        if (intval($order) > 0) {
            $condition .= " AND `displayorder` > :order";
            $params[':order'] = $order;
        }

        $replymod = public_path("addons/whotalk/core/wemod/reply.mod.php");
        if(file_exists($replymod)){
            require_once($replymod);
            $keywords = reply_keywords_search($condition, $params);
            if(empty($keywords)) {
                return $pars;
            }
        }

        $system_module_reply = true;
        foreach($keywords as $keyword) {
            if (!in_array($keyword['module'], array('defalut', 'cover', 'reply'))) {
                $system_module_reply = false;
            }
            $params = array(
                'message' => $message,
                'module' => $keyword['module'],
                'rule' => $keyword['rid'],
                'priority' => $keyword['displayorder'],
                'keyword' => $keyword,
                'reply_type' => $keyword['reply_type']
            );
            $pars[] = $params;
        }
        if (!empty($system_module_reply)) {
            $cache = array(
                'data' => $pars,
                'expire' => TIMESTAMP + 5 * 60,
            );
            Cache::put($cachekey,$cache, 5*60);
        }
        return $pars;
    }

    private function analyzeEvent(&$message) {
        $event = strtolower($message['event']);
        if ($event == 'subscribe') {
            return $this->analyzeSubscribe($message);
        }
        if ($event == 'click') {
            $message['content'] = strval($message['eventkey']);
            return $this->analyzeClick($message);
        }
        if (in_array($event, array('pic_photo_or_album', 'pic_weixin', 'pic_sysphoto'))) {
            pdo_delete('menu_event', array('createtime <' => $GLOBALS['_W']['timestamp'] - 100, 'openid' => $message['from']), 'OR');
            if (!empty($message['sendpicsinfo']['count'])) {
                foreach ($message['sendpicsinfo']['piclist'] as $item) {
                    pdo_insert('menu_event', array(
                        'uniacid' => $GLOBALS['_W']['uniacid'],
                        'keyword' => $message['eventkey'],
                        'type' => $message['event'],
                        'picmd5' => $item,
                        'openid' => $message['from'],
                        'createtime' => TIMESTAMP,
                    ));
                }
            } else {
                pdo_insert('menu_event', array(
                    'uniacid' => $GLOBALS['_W']['uniacid'],
                    'keyword' => $message['eventkey'],
                    'type' => $message['event'],
                    'picmd5' => $item,
                    'openid' => $message['from'],
                    'createtime' => TIMESTAMP,
                ));
            }
            $message['content'] = strval($message['eventkey']);
            $message['source'] = $message['event'];
            return $this->analyzeText($message);
        }
        if (!empty($message['eventkey'])) {
            $message['content'] = strval($message['eventkey']);
            $message['type'] = 'text';
            $message['redirection'] = true;
            $message['source'] = $message['event'];
            return $this->analyzeText($message);
        }
        return $this->handler($message['event']);
    }

    private function analyzeClick(&$message) {
        if(!empty($message['content']) || $message['content'] !== '') {
            $message['type'] = 'text';
            $message['redirection'] = true;
            $message['source'] = 'click';
            return $this->analyzeText($message);
        }

        return array();
    }

    private function analyzeImage(&$message) {
        if (!empty($message['picurl'])) {
            $response = HttpService::ihttp_get($message['picurl']);
            if (!empty($response)) {
                $md5 = md5($response['content']);
                $event = pdo_get('menu_event', array('picmd5' => $md5), array('keyword', 'type'));
                if (!empty($event['keyword'])) {
                    pdo_delete('menu_event', array('picmd5' => $md5));
                } else {
                    $event = pdo_get('menu_event', array('openid' => $message['from']), array('keyword', 'type'));
                }
                if (!empty($event)) {
                    $message['content'] = $event['keyword'];
                    $message['eventkey'] = $event['keyword'];
                    $message['type'] = 'text';
                    $message['event'] = $event['type'];
                    $message['redirection'] = true;
                    $message['source'] = $event['type'];
                    return $this->analyzeText($message);
                }
            }
            return $this->handler('image');
        }
    }

    private function analyzeVoice(&$message) {
        $params = $this->handler('voice');
        if (empty($params) && !empty($message['recognition'])) {
            $message['type'] = 'text';
            $message['redirection'] = true;
            $message['source'] = 'voice';
            $message['content'] = $message['recognition'];
            return $this->analyzeText($message);
        } else {
            return $params;
        }
    }

    private function handler($type) {
        if(empty($type)) {
            return array();
        }
        global $_W;
        $params = array();
        $setting = SettingService::uni_load('default_message',$_W['uniacid']);
        $default_message = $setting['default_message'];
        if(is_array($default_message) && !empty($default_message[$type]['type'])) {
            if ($default_message[$type]['type'] == 'keyword') {
                $message = $this->message;
                $message['type'] = 'text';
                $message['redirection'] = true;
                $message['source'] = $type;
                $message['content'] = $default_message[$type]['keyword'];
                return $this->analyzeText($message);
            } else {
                $params[] = array(
                    'message' => $this->message,
                    'module' => is_array($default_message[$type]) ? $default_message[$type]['module'] : $default_message[$type],
                    'rule' => '-1',
                );
                return $params;
            }
        }
        return array();
    }

    private static function defineConst($obj) {
        global $_W;

        if (!defined('MODULE_ROOT')) {
            define('MODULE_ROOT', public_path('addons/' . $obj->modulename));
        }
        if (!defined('MODULE_URL')) {
            define('MODULE_URL', $_W['siteroot'] . 'addons/' . $obj->modulename . '/');
        }
    }

    private function createModuleProcessor($name){
        global $_W;
        static $file;
        $type = 'processor';
        $class_module = ucfirst($name) . 'Module' . ucfirst($type);
        $type = empty($type) ? 'module' : lcfirst($type);
        if (!class_exists($class_module)) {
            $file = IA_ROOT . "/addons/{$name}/" . $type . '.php';
            if (!is_file($file)) {
                trigger_error($class_module . ' Definition File Not Found', E_USER_WARNING);
                return null;
            }
            require $file;
        }

        if (!class_exists($class_module)) {
            trigger_error($class_module . ' Definition Class Not Found', E_USER_WARNING);
            return null;
        }

        $o = new $class_module();

        $o->uniacid = $o->weid = $_W['uniacid'];
        $o->modulename = $name;
        $o->module = ModuleService::fetch($name);
        $o->__define = $file;
        self::defineConst($o);

        $o->inMobile = defined('IN_MOBILE');
        return $o;
    }

    private function process($param) {
        global $_W;
        if(empty($param['module']) || !in_array($param['module'], $this->modules)) {
            return false;
        }
        if ($param['module'] == 'reply') {
            return true;
        } else {
            $processor = self::createModuleProcessor($param['module']);
        }
        $processor->rule = $param['rule'];
        $processor->reply_type = $param['reply_type'];
        $processor->priority = intval($param['priority']);
        $processor->inContext = $param['context'] === true;
        $response = $processor->respond($param['message']);
        if(empty($response)) {
            return false;
        }

        return $response;
    }

    public function died($content = '') {
        global $_W, $engine;
        if (empty($content)) {
            exit('');
        }
        $response['FromUserName'] = $engine->message['to'];
        $response['ToUserName'] = $engine->message['from'];
        $response['MsgType'] = 'text';
        $response['Content'] = htmlspecialchars_decode($content);
        $response['CreateTime'] = TIMESTAMP;
        $response['FuncFlag'] = 0;
        $xml = array2xml($response);
        if(!empty($_GET['encrypt_type']) && $_GET['encrypt_type'] == 'aes') {
            $resp = $engine->account->encryptMsg($xml);
            $resp = $engine->account->xmlDetract($resp);
        } else {
            $resp = $xml;
        }
        exit($resp);
    }

}
