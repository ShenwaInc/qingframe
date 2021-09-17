<?php

namespace App\Utils;

use App\Models\Account;
use App\Services\CacheService;
use App\Services\UserService;
use App\Services\WeauthService;
use App\Services\WechatService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WeAccount extends \ArrayObject{

    public $uniacid = 0;
    protected $account;
    protected $owner = array();

    protected $groups = array();
    protected $setting = array();
    protected $startTime;
    protected $endTime;
    protected $groupLevel;
    protected $switchUrl;
    protected $displayUrl;
    protected $setMeal = array();
    protected $sameAccountExist;
    protected $menuFrame;
    protected $type;
    protected $tablename;
    protected $typeName;
    protected $typeSign;
    protected $typeTemplate;
    public $supportVersion = 0;
    protected $supportOauthInfo;
    protected $supportJssdk;

    protected $toArrayMap = array(
        'type_sign' => 'typeSign',
        'createtime' => 'createTime',
        'starttime' => 'startTime',
        'endtime' => 'endTime',
        'groups' => 'groups',
        'setting' => 'setting',
        'grouplevel' => 'groupLevel',
        'type_name' => 'typeName',
        'switchurl' => 'switchUrl',
        'setmeal' => 'setMeal',
        'current_user_role' => 'CurrentUserRole',
        'is_star' => 'isStar',
    );
    private static $accountObj = array();

    public function __construct($uniaccount = array()) {
        $this->uniacid = $uniaccount['uniacid'];
        $cachekey = CacheService::system_key('uniaccount', array('uniacid' => $this->uniacid));
        $cache = Cache::get($cachekey, array());
        if (empty($cache)) {
            $this->account = $uniaccount;
            $cache = $this->getAccountInfo($this->uniacid);
            Cache::put($cachekey, $cache,7*86400);
        }
        $this->account = array_merge((array) $cache, $uniaccount);
    }

    public static function create($acidOrAccount = array()) {
        global $_W;
        $uniaccount = array();
        if (is_object($acidOrAccount) && $acidOrAccount instanceof self) {
            return $acidOrAccount;
        }
        if (is_array($acidOrAccount) && !empty($acidOrAccount)) {
            $uniaccount = $acidOrAccount;
        } else {
            if (!empty($acidOrAccount)) {
                $uniaccount = Account::getByAcid(intval($acidOrAccount));
            } elseif(!empty($_W['account']['uniacid'])) {
                $uniaccount = Account::getByUniacid($_W['account']['uniacid']);
            }
        }
        if (is_error($uniaccount) || empty($uniaccount)) {
            $uniaccount = $_W['account'];
        }
        if (!empty(self::$accountObj[$uniaccount['uniacid']])) {
            return self::$accountObj[$uniaccount['uniacid']];
        }
        if (!empty($uniaccount) && isset($uniaccount['type']) || !empty($uniaccount['isdeleted'])) {
            return self::includes($uniaccount);
        } else {
            return error('-1', '帐号不存在或是已经被删除');
        }
    }

    public static function includes($uniaccount) {
        $account_obj = new WechatService($uniaccount);
        self::$accountObj[$uniaccount['uniacid']] = $account_obj;
        return $account_obj;
    }

    static function createByUniacid($uniacid = 0) {
        global $_W;
        $uniacid = intval($uniacid) > 0 ? intval($uniacid) : $_W['uniacid'];
        if (!empty(self::$accountObj[$uniacid])) {
            return self::$accountObj[$uniacid];
        }
        $uniaccount = Account::getByUniacid($uniacid);
        if (empty($uniaccount)) {
            return error('-1', '帐号不存在或是已经被删除');
        }
        if (!empty($_W['uid']) && !$_W['isadmin'] && !UserService::AccountRole($_W['uid'], $uniacid)) {
            return error('-1', '无权限操作该平台账号');
        }
        return self::create($uniaccount);
    }

    protected function fetchGroups() {
        $groups = DB::table('mc_groups')->where('uniacid',$this->uniacid)->get();
        if (!empty($groups)){
            $this->groups = $groups->toArray();
        }
        return $this->groups;
    }

    public function __toArray() {
        foreach ($this->account as $key => $property) {
            $this[$key] = $property;
        }

        foreach ($this->toArrayMap as $key => $type) {
            if (isset($this->$type) && !empty($this->$type)) {
                $this[$key] = $this->$type;
            } else {
                $this[$key] = $this->__get($type);
            }
        }

        return $this;
    }

    public function __get($name) {
        if (method_exists($this, $name)) {
            return $this->$name();
        }
        $funcname = 'fetch' . ucfirst($name);
        if (method_exists($this, $funcname)) {
            return $this->$funcname();
        }
        if (isset($this->$name)) {
            return $this->$name;
        }

        return false;
    }

}
