<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CorePaylog;
use App\Models\Setting;
use App\Services\AccountService;
use App\Services\CacheService;
use App\Services\ModuleService;
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
    public $entrys = array(
        'account'=>'平台管理',
        'module'=>'应用模块',
        'server'=>'功能服务'
    );

    function accInit($check=false){
        global $_W,$_GPC;
        $uniacid = intval($_GPC['uniacid']);
        $this->account = Account::getByUniacid($uniacid);
        if (!empty($this->account)){
            $this->uniacid = $uniacid;
            $this->role = UserService::AccountRole($_W['uid'],$uniacid);
        }
        if (empty($this->role)){
            //暂无权限
            return error(-1, "暂无权限");
        }
        if ($check){
            if ($this->uniacid==0 || $this->account['isdeleted']==1) return error(-1, "找不到该平台，可能已被删除");
            if ($this->account['endtime']>0 && $this->account['endtime']<TIMESTAMP && !$_W['isfounder']){
                return error(-1, "该平台服务已到期，请联系管理员处理");
            }
        }
        $_W['account'] = $this->account;
        $_W['page']['title'] = $this->account['name'];
        return $this->account;
    }

    //平台管理控制器
    public function index(Request $request,$action='profile'){
        $check = in_array($action, array('component','setting'));
        $account = $this->accInit($check);
        if (is_error($account)){
            return $this->message($account['message'], '/console');
        }
        $method = "do".ucfirst($action);
        if (method_exists($this, $method)){
            return $this->$method($request);
        }
        return $this->message('敬请期待');
    }

    public function doRole(Request $request){
        global $_W;
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
        $return['account'] = $this->account;
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

    public function doFunctions(){
        global $_W;
        $account = $this->account;
        $return = array('title'=>'平台管理','account'=>$account,'uniacid'=>$this->uniacid);
        $return['role'] = $this->role;
        session()->put('uniacid', $account['uniacid']);
        //读取可用服务
        $servers = pdo_getall("microserver_unilink", array('status'=>1));
        //判断微服务权限，待完善
        $return['servers'] = $servers;
        $return['components'] = [];

        //读取可用模块
        $components = AccountService::ExtraModules($_W['uniacid']);
        if (empty($components) && !empty($_W['config']['defaultmodule'])){
            $defaultModule = pdo_get("modules", array('name'=>$_W['config']['defaultmodule'], 'application_type'=>1));
            if (!empty($defaultModule)){
                $components = [['name'=>$defaultModule['title'],'identity'=>$defaultModule['name'],'logo'=>$defaultModule['logo']]];
                DB::table('uni_account_extra_modules')->insert(array(
                    'uniacid'=>$this->uniacid,
                    'modules'=>serialize($components)
                ));
                $return['components'] = $components;
            }
        }else{
            foreach ($components as $component){
                //判断模块是否可用
                $module = ModuleService::fetch($component['identity']);
                if (empty($module)) continue;
                $return['components'][] = $component;
            }
        }
        //判断当前用户模块权限（操作员/管理员）
        if (!$_W['isfounder']){
            //获取权限
            $permission= DB::table('users_permission')->where(['uid'=>$_W['uid'],'uniacid'=>$this->uniacid])->value('permission');
            //为空默认有全部权限(未设置过权限)
            if(!empty($permission)){
                $permission=unserialize($permission);
                foreach ($return['components'] as $key => $value){
                    //没有权限，移除本应用模块
                    if(empty($permission['modules'][$value['identity']])){
                        unset($return['components'][$key]);
                    }
                }
                foreach ($return['servers'] as $key => $value){
                    //没有权限，移除本服务
                    if(empty($permission['servers'][$value['name']])){
                        unset($return['servers'][$key]);
                    }
                }
            }
        }
        return $this->globalview('console.account.functions',$return);
    }

    public function doModules(Request $request){
        global $_W;
        $enabled_modules = ModuleService::moduleList();
        if ($request->isMethod('post')){
            $extraModules = $request->input('extras_modules');
            $modules = [];
            if (!empty($extraModules)){
                foreach ($extraModules as $key=>$extra){
                    if (empty($enabled_modules[$key])) continue;
                    if (empty($extra)) continue;
                    $modules[] = array(
                        'name'=>$enabled_modules[$key]['title'],
                        'identity'=>$key,
                        'logo'=>$enabled_modules[$key]['logo']
                    );
                }
            }
            if (!DB::table('uni_account_extra_modules')->updateOrInsert(array('uniacid'=>$this->uniacid), array('modules'=>serialize($modules)))){
                return $this->message('保存失败，请重试');
            }
            CacheService::flush();
            return $this->message('操作成功！',wurl('account/functions',array('uniacid'=>$this->uniacid),true), 'success');
        }
        $return = array('title'=>'编辑平台模块权限', 'modules'=>[]);
        $return['extras'] = AccountService::ExtraModules($_W['uniacid']);
        if (!empty($enabled_modules)){
            foreach ($enabled_modules as $key=>$value){
                $module = ModuleService::fetch($key);
                if (empty($module)) continue;
                $return['modules'][$key] = $value;
            }
        }
        return $this->globalview('console.account.modules',$return);
    }

    public function doEntry(){
        global $_W, $_GPC;
        if (checksubmit()){
            $controller = trim($_GPC['ctrl']);
            if (empty($controller)) return $this->message("入口分类不能为空");
            $method = trim($_GPC['methods'][$controller]);
            if (empty($method)) return $this->message("默认入口不能为空");
            $condition = array(
                'uid'=>$_W['uid'],
                'uniacid'=>$this->uniacid
            );
            $account_users = DB::table('uni_account_users')->where($condition)->first();
            if (empty($account_users)){
                $condition['role'] = $this->role;
                $condition['entrance'] = $controller.":".$method;
                $complete = DB::table('uni_account_users')->insert($condition);
            }else{
                $complete = DB::table('uni_account_users')->where('id', $account_users['id'])->update(array('entrance'=>$controller.":".$method));
            }
            if (!$complete){
                return $this->message();
            }
            return $this->message("保存成功！", referer(), 'success');
        }
        list($controller, $method) = AccountService::GetEntrance($_W['uid'], $this->uniacid);
        $entrances = AccountService::GetAllEntrances($this->uniacid);
        return $this->globalview('console.account.entry',array(
            'title'=>'默认入口设置',
            'uniacid'=>$this->uniacid,
            'ctrl'=>$controller,
            'method'=>$method,
            'entrances'=>$entrances,
            'titles'=>$this->entrys
        ));
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
        list($entry, $method) = AccountService::GetEntrance($_W['uid'], $this->uniacid);
        $entrances = AccountService::GetAllEntrances($this->uniacid);
        $return['entrance'] = $this->entrys[$entry]. "&nbsp;&gt;&nbsp;";
        $return['entrance'] .= $entrances[$entry][$method];
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
        DB::table('users_operate_history')->where('uniacid', $uniacid)->delete();
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
                'description' => trim($post['description']),
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

                return $this->message('恭喜您，创建成功！',wurl('account/profile',array('uniacid'=>$uniacid)),'success');
            }
            return $this->message('创建失败，请重试');
        }
        $return = array('title'=>'创建平台');
        return $this->globalview('console.account.create', $return);
    }
    public function doPermission(Request $request){
        $uid=$request->input('uid');
        $uniacid=$request->input('uniacid');
        //获取权限
        $permissionInfo= DB::table('users_permission')->where(['uid'=>$uid,'uniacid'=>$this->uniacid])->first();

        //保存权限
        if ($request->isMethod('post')){
            $routesData=$request->input('routes');
            $permission=serialize($routesData);

            if(!empty($permissionInfo)){
                $res=DB::table('users_permission')->where(['id'=>$permissionInfo['id']])->update(['permission'=>$permission]);
            }else{
                $data=[
                    'uniacid'=>$uniacid,
                    'uid'=>$uid,
                    'permission'=>$permission
                ];
                $res=DB::table('users_permission')->insert($data);
            }

            if($res) return $this->message('保存成功',wurl('account/role',array('uniacid'=>$uniacid)),'success');

            return $this->message('保存失败，请重试');
        }
        //获取已安装应用
        $modulesList = ModuleService::moduleList();
        $permission=unserialize($permissionInfo['permission'] ?? []);
        $components = AccountService::ExtraModules($uniacid);

        foreach ($modulesList as $key  => &$value){
            //未添加，移除应用
            if(empty($components[$key])){
                unset($modulesList[$key]);
                continue;
            }

            $value['permissions']=unserialize($value['permissions'] ?? '');
            if(empty($value['permissions'])) continue;

            $currentPermission=$permission['modules'][$key] ?? null;
            if(empty($currentPermission)) continue;

            //比较是否已设置权限
            foreach ($value['permissions'] as &$val){
                $val['exist']=in_array($val['route'],$currentPermission) ? true : false;

                //二级权限
                foreach ($val['subPerm'] ?? [] as $k => $v){
                    $val['subPerm'][$k]['exist']=in_array($v['route'],$currentPermission) ? true : false;
                }
            }
            unset($val);
        }
        unset($value);
        //读取可用服务
        $serversList = pdo_getall("microserver_unilink", array('status'=>1));
        foreach ($serversList as &$value){

            $value['perms']=unserialize($value['perms'] ?? '');

            if(empty($value['perms']))
                $value['perms']=['name'=>'微服务入口','route'=>'entrance'];


            $currentPermission=$permission['servers'][$value['name']] ?? [];

            //比较是否已设置权限
            foreach ($value['perms'] as $ke => &$val){
                $val=['name'=>$val,'route'=>$ke];
                $val['exist']=in_array($val['route'],$currentPermission) ? true : false;

                //二级权限
                foreach ($val['subPerm'] ?? [] as $k => $v){
                    $val['subPerm'][$k]['exist']=in_array($v['route'],$currentPermission) ? true : false;
                }
            }
            unset($val);
        }
        unset($value);
        return $this->globalview('console.account.permission',[
            'uid'         => $uid,
            'uniacid'     => $uniacid,
            'modulesList' => $modulesList,
            'serversList'     => $serversList,
        ]);
    }

}
