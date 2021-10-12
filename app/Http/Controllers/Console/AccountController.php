<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CorePaylog;
use App\Models\Setting;
use App\Services\CacheService;
use App\Services\PayService;
use App\Services\SettingService;
use App\Services\UserService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\Psr7\str;

class AccountController extends Controller
{

    public $uniacid = 0;
    public $role = 'operator';
    public $account = null;

    function accInit(){
        global $_W,$_GPC;
        $uniacid = intval($_GPC['uniacid']);
        $this->account = Account::getByUniacid($uniacid);
        if (!empty($this->account)){
            $this->uniacid = $uniacid;
            $this->role = UserService::AccountRole($_W['uid'],$uniacid);
        }
        if (empty($this->role)){
            //暂无权限
            echo response()->view('message',array('message'=>'暂无权限','redirect'=>'/console','type'=>'error','_W'=>$_W))->content();
            session()->save();
            exit();
        }
    }

    //平台管理控制器
    public function index(Request $request,$action='profile'){
        $this->accInit();
        $method = "do".ucfirst($action);
        if (method_exists($this, $method)){
            return $this->$method($request);
        }
        return $this->message('敬请期待');
    }

    public function doComponent(Request $request){
        global $_W;
        if (!$_W['isfounder']){
            return $this->message('暂无权限，请勿乱操作');
        }
        $return = array('title'=>'应用权限','uniacid'=>$this->uniacid,'components'=>array());
        if ($this->uniacid==0 || $this->account['isdeleted']==1) return $this->message('找不到该平台，可能已被删除');
        if ($this->account['endtime']>0 && $this->account['endtime']<TIMESTAMP && !$_W['isfounder']){
            return $this->message('该平台服务已到期，请联系管理员处理');
        }

        $components = DB::table('uni_account_extra_modules')->where('uniacid',$this->uniacid)->first();
        if (empty($components)){
            $defaultmodule = DB::table('gxswa_cloud')->where('modulename',$_W['config']['defaultmodule'])->select(['name','modulename','logo'])->first();
            $components = [['name'=>$defaultmodule['name'],'identity'=>$defaultmodule['modulename'],'logo'=>$defaultmodule['logo']]];
            DB::table('uni_account_extra_modules')->insert(array(
                'uniacid'=>$this->uniacid,
                'modules'=>serialize($components)
            ));
            $return['components'] = $components;
        }else{
            $return['components'] = unserialize($components['modules']);
        }
        return $this->globalview('console.account.com',$return);
    }

    public function doRole(Request $request){
        global $_W;
        if ($this->uniacid==0 || $this->account['isdeleted']==1) return $this->message('找不到该平台，可能已被删除');
        if ($this->account['endtime']>0 && $this->account['endtime']<TIMESTAMP && !$_W['isfounder']){
            return $this->message('该平台服务已到期，请联系管理员处理');
        }
        if ($this->role!='owner' && !$_W['isfounder'])return $this->message('您暂无权限操作');
        $return = array('title'=>'管理权限','users'=>array(),'uniacid'=>$this->uniacid,'role'=>$this->role);
        $subs = UserService::GetSubs($_W['uid']);
        $op = $request->input('op','');
        if ($request->isMethod('post')){
            if ($op=='add') {
                $uid = (int)$request->input('uid', 0);
                if ($uid == 0) return $this->message('找不到该用户，可能已被删除');
                if (!isset($subs[$uid]) && !$_W['isfounder']) {
                    return $this->message('您暂时无权操作该用户');
                }
                $role = (string)$request->input('role', '');
                if (!in_array($role, array('manager', 'operator'))) {
                    return $this->message('权限角色不正确');
                }
                $complete = UserService::AccountRoleUpdate($this->uniacid, $uid, $role);
                if ($complete) return $this->message('保存成功！', wurl('account/role', array('uniacid' => $this->uniacid)), 'success');
            }elseif ($op=='setowner'){
                if (!$_W['isfounder']){
                    return $this->message('您暂无权限操作');
                }
                $uid = (int)$request->input('uid',0);
                if ($uid==0) return $this->message('找不到该用户，可能已被删除');
                DB::table('uni_account_users')->where(array('role'=>'owner','uniacid'=>$this->uniacid))->delete();
                $complete = UserService::AccountRoleUpdate($this->uniacid, $uid);
                if ($complete) return $this->message('保存成功！', wurl('account/role', array('uniacid' => $this->uniacid)), 'success');
            }
            return $this->message();
        }
        if ($op=='add'){
            $return['subusers'] = $subs;
            $return['owner'] = (int)DB::table('uni_account_users')->where(array('uniacid'=>$this->uniacid,'role'=>'owner'))->value('uid');
            return $this->globalview('console.account.roleadd',$return);
        }elseif ($op=='remove'){
            $uid = (int)$request->input('uid',0);
            if ($uid==0) return $this->message('找不到该用户，可能已被删除');
            $complete = DB::table('uni_account_users')->where(array('uid'=>$uid,'uniacid'=>$this->uniacid))->delete();
            if ($complete){
                //删除操作痕迹，待完善
                return $this->message('操作成功！',wurl('account/role',array('uniacid'=>$this->uniacid)),'success');
            }
            return $this->message();
        }
        $users = DB::table('users')->leftJoin('uni_account_users','uni_account_users.uid','=','users.uid')
            ->where('uni_account_users.uniacid',$this->uniacid)
            ->whereIn('uni_account_users.role',array('owner','manager','operator'))->get()->toArray();
        if (!empty($users)){
            $roles = array('owner'=>'所有者','manager'=>'管理员','operator'=>'操作员');
            foreach ($users as &$user){
                $user['roler'] = $roles[$user['role']];
                $user['permission'] = array();
                $user['expired'] = false;
                if ($user['endtime']>0 && $user['endtime']<=TIMESTAMP){
                    $user['expired'] = true;
                }
                $user['subuser'] = false;
                if (isset($subs[$user['uid']]) || $_W['isfounder']){
                    $user['subuser'] = true;
                }
                if ($user['role']!='owner'){
                    $permission = DB::table('users_permission')->where(array('uid'=>$user['uid'],'uniacid'=>$this->uniacid))->value('permission');
                    if (!empty($permission)){
                        $user['permission'] = unserialize($permission);
                    }
                }
            }
            $return['users'] = $users;
        }
        $return['subusers'] = $subs;
        $return['colors'] = array('owner'=>'orange','manager'=>'green','operator'=>'blue');
        return $this->globalview('console.account.role',$return);
    }

    public function doApiverify(Request $request){
        $field = 'file';
        if ($request->hasFile($field)){
            $Upload = $request->file($field);
            $ext = $Upload->getClientOriginalExtension();
            if ($ext!='txt') return $this->message('仅限上传TXT格式文件');
            $filename = htmlspecialchars_decode($Upload->getClientOriginalName(), ENT_QUOTES);
            $content = file_get_contents($Upload);
            $filepath = public_path($filename);
            $writer = fopen($filepath,'w');
            $complete = fwrite($writer, $content);
            fclose($writer);
            if ($complete) return $this->message('上传成功！','' ,'success');
        }
        return $this->message();
    }

    public function doSetting(Request $request){
        global $_W;
        if ($this->uniacid==0 || $this->account['isdeleted']==1) return $this->message('找不到该平台，可能已被删除');
        if ($this->account['endtime']>0 && $this->account['endtime']<TIMESTAMP && !$_W['isfounder']){
            return $this->message('该平台服务已到期，请联系管理员处理');
        }
        $setting = SettingService::uni_load('', $this->uniacid);
        if (empty($setting['payment'])){
            $setting['payment'] = array(
                'credit'=>array('pay_switch'=>0),
                'alipay'=>array('pay_switch'=>0,'account'=>'','partner'=>'','secret'=>''),
                'wechat'=>array('pay_switch'=>0,'mchid'=>'','apikey'=>'','')
            );
        }
        if (empty($setting['notify'])){
            $setting['notify'] = array(
                'sms'=>array('switch'=>0,'type'=>''),
                'email'=>array('switch'=>0,'smtp'=>'','port'=>'','username'=>'','password'=>'','sender'=>'')
            );
        }
        $op = (string)$request->input('op','');
        if ($request->isMethod('post')){
            switch ($op){
                case 'js-switch' :
                    $name = $request->input('name','');
                    $config = explode('.',$name);
                    if (empty($config)) return $this->message('未指定配置项');
                    if ($name=='payment.alipay.pay_switch'){
                        $alipay = $setting['payment']['alipay'];
                        if (empty($alipay['account']) || empty($alipay['partner'] || $alipay['secret'])){
                            return $this->message('请先配置支付宝接口',wurl('account/setting',array('uniacid'=>$this->uniacid)));
                        }
                    }
                    if ($name=='payment.wechat.pay_switch'){
                        $wechat = $setting['payment']['wechat'];
                        if (empty($wechat['mchid']) || empty($wechat['apikey'])){
                            return $this->message('请先配置微信支付接口',wurl('account/setting',array('uniacid'=>$this->uniacid)));
                        }
                    }
                    $value = (int)$request->input('value',0);
                    $key = $config[0];
                    if (isset($config[2])){
                        $setting[$key][$config[1]][$config[2]] = $value;
                    }elseif(isset($config[1])){
                        $setting[$key][$config[1]] = $value;
                    }else{
                        $setting[$key] = $value;
                    }
                    $update = is_array($setting[$key]) ? serialize($setting[$key]) : $setting[$key];
                    $complete = SettingService::uni_save($this->uniacid,$key,$update);
                    if ($complete){
                        return $this->message('保存成功！',wurl('account/setting',array('uniacid'=>$this->uniacid)), 'success');
                    }
                    break;
                case 'save-wechat' :
                    $wechat = $request->input('wechat',array());
                    if (empty($wechat['mchid']) || empty($wechat['apikey'])){
                        return $this->message('微信支付接口配置不正确');
                    }
                    $wechat['pay_switch'] = $setting['payment']['wechat']['pay_switch'];
                    $setting['payment']['wechat'] = $wechat;
                    $complete = SettingService::uni_save($this->uniacid,'payment',serialize($setting['payment']));
                    if ($complete){
                        return $this->message('保存成功！',wurl('account/setting',array('uniacid'=>$this->uniacid)), 'success');
                    }
                    break;
                case 'save-alipay' :
                    $alipay = $request->input('alipay',array());
                    if (empty($alipay['account']) || empty($alipay['partner'] || $alipay['secret'])){
                        return $this->message('支付宝接口配置未完善');
                    }
                    $alipay['pay_switch'] = $setting['payment']['alipay']['pay_switch'];
                    $setting['payment']['alipay'] = $alipay;
                    $complete = SettingService::uni_save($this->uniacid,'payment',serialize($setting['payment']));
                    if ($complete){
                        return $this->message('保存成功！',wurl('account/setting',array('uniacid'=>$this->uniacid)), 'success');
                    }
                    break;
                default :
                    break;
            }
            return $this->message();
        }
        if ($op=='demo-alipay'){
            $_W['uniacid'] = $this->uniacid;
            $_W['account'] = uni_fetch($this->uniacid);
            $testfee = floatval(0.1 + (random(1,true) / 100));
            $orderinfo = [
                'openid'=>$_W['uid'],
                'tid'=>random(10),
                'fee'=>$testfee,
                'tag'=>"测试充值{$testfee}元",
                'is_usecard'=>0,
                'card_type'=>0,
                'card_id'=>0,
                'card_fee'=>0,
                'encrypt_code'=>random(6,true),
                'is_wish'=>0
            ];
            $paylog = CorePaylog::create($orderinfo);

            $orderinfo = [
                'uniontid'=>$paylog['uniontid'],
                'tag'=>$paylog['tag'],
                'fee'=>$paylog['fee']
            ];
            $info = PayService::create('alipay',$orderinfo);

            dd($info);
        }
        return $this->globalview('console.account.setting', array(
            'uniacid'=>$this->uniacid,
            'account'=>$this->account,
            'setting'=>$setting,
            'role'=>$this->role,
            'title'=>'平台配置'
        ));
    }

    public function doEdit(Request $request){
        if (empty($this->account)){
            return $this->message('找不到该平台，可能已被删除');
        }
        if ($request->isMethod('post')){
            $post = $request->input('data');
            if (empty($post['name'])) return $this->message('平台名称不能为空');
            if (empty($post['logo'])) return $this->message('请上传平台LOGO');
            $post['description'] = trim($post['description']);
            $complete = Account::where('uniacid',$this->uniacid)->update($post);
            if (!$complete) return $this->message('保存失败，请重试');
            return $this->message('保存成功！',wurl('account/profile',array('uniacid'=>$this->uniacid),true), 'success');
        }
        return $this->globalview('console.account.edit',array('title'=>'编辑平台信息','account'=>$this->account));
    }

    public function doProfile(Request $request){
        global $_W;
        $account = $this->account;
        if (empty($account) || $account['isdeleted']==1) return $this->message('找不到该平台，可能已被删除');
        if ($account['endtime']>0 && $account['endtime']<TIMESTAMP && !$_W['isfounder']){
            return $this->message('该平台服务已到期，请联系管理员处理');
        }
        if ($request->isMethod('post')){
            $op = $request->input('op');
            if ($op=='setexpire'){
                $data = array('endtime'=>0);
                $expire = (string)$request->input('expire','');
                if ($expire!=''){
                    $data['endtime'] = strtotime($expire);
                }
                $complete = DB::table('account')->where('acid',$account['acid'])->update($data);
                if (!$complete) return $this->message('保存失败，请重试');
                return $this->message('保存成功！',wurl('account/profile',array('uniacid'=>$account['uniacid']),true), 'success');
            }
        }
        $account['expirdate'] = $account['endtime']>0 ? date('Y-m-d',$account['endtime']) : '永久';
        $return = array('title'=>'平台管理','account'=>$account,'uniacid'=>$this->uniacid);
        $return['role'] = $this->role;
        return $this->globalview('console.account.profile',$return);
    }

    public function doRemove(Request $request){
        //查询权限
        $uniacid = (int)$request->input('uniacid',0);
        if ($uniacid==0) return $this->message('请选择要删除的平台');
        global $_W;
        $role = UserService::AccountRole($_W['uid'],$uniacid);
        if (!in_array($role,array('founder','owner')) && !$_W['isfounder']){
            return $this->message('暂无权限，请勿乱操作');
        }
        //删除平台
        DB::table('account')->where('uniacid',$uniacid)->update(array('isdeleted'=>1));
        DB::table('uni_modules')->where('uniacid',$uniacid)->delete();
        DB::table('users_operate_star')->where('uniacid',$uniacid)->delete();
        DB::table('users_lastuse')->where('uniacid',$uniacid)->delete();
        DB::table('core_menu_shortcut')->where('uniacid',$uniacid)->delete();
        DB::table('uni_link_uniacid')->where('uniacid',$uniacid)->delete();
        $cachekey = CacheService::system_key('user_accounts', array('type' => 'account', 'uid' => $_W['uid']));
        Cache::forget($cachekey);
        $cachekey = CacheService::system_key('uniaccount', array('uniacid' => $uniacid));
        Cache::forget($cachekey);
        return $this->message('删除成功！',url('console'),'success');
    }

    public function doCreate(Request $request){
        if ($request->isMethod('post')){
            global $_W;
            $post = $request->input('data');
            if (empty($post['name'])) return $this->message('平台名称不能为空');
            if (empty($post['logo'])) return $this->message('请上传平台LOGO');
            $uni_account = DB::table('uni_account');
            $uniacid = $uni_account->insertGetId(array(
                'groupid' => 0,
                'default_acid' => 0,
                'name' => $post['name'],
                'description' => $post['description'],
                'logo'=>$post['logo'],
                'title_initial' => 'W',
                'createtime' => TIMESTAMP,
                'create_uid' => $_W['uid']
            ));
            if (!empty($uniacid)){
                $acid = Account::account_create($uniacid,array('name'=>$post['name']));
                $uni_account->where('uniacid',$uniacid)->update(array('default_acid' => $acid));
                UserService::AccountRoleUpdate($uniacid,$_W['uid']);

                DB::table('mc_groups')->insert(array('uniacid' => $uniacid, 'title' => '默认会员组', 'isdefault' => 1));
                DB::table('uni_settings')->insert(array(
                    'creditnames' => serialize(array('credit1' => array('title' => '积分', 'enabled' => 1), 'credit2' => array('title' => '余额', 'enabled' => 1))),
                    'creditbehaviors' => serialize(array('activity' => 'credit1', 'currency' => 'credit2')),
                    'uniacid' => $uniacid,
                    'default_site' => 0,
                    'sync' => serialize(array('switch' => 0, 'acid' => '')),
                ));

                return $this->message('恭喜您，创建成功！',url('console/account',array('uniacid'=>$uniacid)),'success');
            }
            return $this->message('创建失败，请重试');
        }
        $return = array('title'=>'创建平台');
        return $this->globalview('console.account.create', $return);
    }

}
