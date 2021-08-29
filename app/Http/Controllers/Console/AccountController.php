<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\CacheService;
use App\Services\SettingService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{

    public $uniacid = 0;
    public $role = 'operator';
    public $account = null;

    function __construct(Request $request){
        global $_W;
        $uniacid = (int)$request->input('uniacid');
        $this->account = Account::getByUniacid($uniacid);
        if (!empty($this->account)){
            $this->uniacid = $uniacid;
            $this->role = UserService::AccountRole($_W['uid'],$uniacid);
        }
    }

    //平台管理控制器
    public function index(Request $request,$action='profile'){
        $method = "do".ucfirst($action);
        if (method_exists($this, $method)){
            return $this->$method($request);
        }
        return $this->message('敬请期待');
    }

    public function doSetting(Request $request){
        $setting = SettingService::uni_load('', $this->uniacid);
        if (empty($setting['payment'])){
            $setting['payment'] = array(
                'credit'=>array('pay_switch'=>0),
                'alipay'=>array('pay_switch'=>0,'account'=>'','partner'=>'','secret'=>''),
                'wechat'=>array('pay_switch'=>0,'mchid'=>'','apikey'=>'','')
            );
        }
        if ($request->isMethod('post')){

        }
        return $this->globalview('console.account.setting', array(
            'uniacid'=>$this->uniacid,
            'account'=>$this->account,
            'setting'=>$setting
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
