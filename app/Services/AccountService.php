<?php


namespace App\Services;


use App\Models\Account;
use ArrayObject;
use Illuminate\Support\Facades\DB;

class AccountService extends ArrayObject {

    public $uniacid = 0;
    protected $account;
    protected $owner = array();

    protected $groups = array();
    protected $setting = array();
    protected $startTime;
    protected $endTime;
    protected $groupLevel;
    protected $logo;
    protected $qrcode;
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

    static function GetType($type = 0){
        $all_account_type = array(
            1=>array(
                'title' => '公众号',
                'type_sign' => 'account',
                'table_name' => 'account_wechats',
                'module_support_name' => 'account_support',
                'module_support_value' => 2,
                'store_type_module' => 1,
                'store_type_number' => 2,
                'store_type_renew' => 7
            )
        );
        if (!empty($type)) {
            return !empty($all_account_type[$type]) ? $all_account_type[$type] : array();
        }
        return $all_account_type;
    }

    static function GetTypeSign($type_sign = ''){
        $all_account_type_sign = array(
            'account' => array(
                'contain_type' => array(1, 3),
                'level' => array(1 => '订阅号', 2 => '服务号', 3 => '认证订阅号', 4 => '认证服务号'),
                'icon' => 'wi wi-wx-circle',
                'createurl' => url('account/post'),
                'title' => '公众号'
            )
        );
        if (!empty($type_sign)) {
            return !empty($all_account_type_sign[$type_sign]) ? $all_account_type_sign[$type_sign] : array();
        }
        return $all_account_type_sign;
    }

    static function GetOprateStar($uid,$uniacid,$module_name){
        return DB::table('users_operate_star')->where(array(
            ['uid',$uid],
            ['uniacid',$uniacid],
            ['module_name',$module_name]
        ))->first();
    }

    static function createByUniacid($uniacid = 0) {
        global $_W;
        $uniacid = intval($uniacid) > 0 ? intval($uniacid) : $_W['uniacid'];
        if (!empty(self::$accountObj[$uniacid])) {
            return self::$accountObj[$uniacid];
        }
        $uniaccount = Account::getUniAccountByUniacid($uniacid);
        if (empty($uniaccount)) {
            return error('-1', '帐号不存在或是已经被删除');
        }
        if (!empty($_W['uid']) && !$_W['isadmin'] && !UserService::AccountRole($_W['uid'], $uniacid)) {
            return error('-1', '无权限操作该平台账号');
        }
        return self::create($uniaccount);
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
                $uniaccount = Account::getUniAccountByAcid(intval($acidOrAccount));
            } elseif(!empty($_W['account']['uniacid'])) {
                $uniaccount = Account::getUniAccountByUniacid($_W['account']['uniacid']);
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

        $type = $uniaccount['type'];
        $account_obj = new WechatService($uniaccount);
        $account_obj->type = $type;
        self::$accountObj[$uniaccount['uniacid']] = $account_obj;

        return $account_obj;
    }

    static function Create_info() {
        global $_W;
        $account_create_info = PermissionService::UserAccountNum();
        $can_create = false;
        if ($_W['isadmin'] || (!empty($account_create_info['account_limit']) && (!empty($account_create_info['founder_account_limit']) && $_W['user']['owner_uid'] || empty($_W['user']['owner_uid'])) || !empty($account_create_info['store_account_limit']))){
            $can_create = true;
        }
        $all_account_type_sign = self::GetTypeSign();
        $all_account_type_sign['account']['can_create'] = $can_create;
        return $all_account_type_sign;
    }

    static function OwnerAccountNums($uid, $role){
        $account_all_type = self::GetType();
        $account_all_type_sign = array('account');

        $num = array('account_num'=>0);

        foreach ($account_all_type_sign as $type_info) {
            $key_name = $type_info . 'account_num';
            $num[$key_name] = 0;
        }

        $uniacocunts = Account::searchAccountList();

        if (!empty($uniacocunts)) {
            $uni_account_users_table = DB::table('uni_account_users')->join('account','uni_account_users.uniacid','=','account.uniacid');
            $all_account = $uni_account_users_table->where(array(
                ['uni_account_users.role',$role],
                ['uni_account_users.uid',$uid]
            ))->get()->keyBy('uniacid')->toArray();

            foreach ($all_account as $account) {
                foreach ($account_all_type as $type_key => $type_info) {
                    if ($type_key == $account['type']) {
                        $key_name = $type_info['type_sign'] . '_num';
                        $num[$key_name] += 1;
                        continue;
                    }
                }
            }
        }

        return $num;
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
        if ('module' == $name) {
            if (!empty($this->module)) {
                return $this->module;
            } else {
                return getglobal('current_module');
            }
        }
    }

}
