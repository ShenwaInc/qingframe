<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\CacheService;
use App\Services\ModuleService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{

    public $uniacid = 0;
    public $role = 'operator';
    public $account = null;
    public $entrance = array(
        'account'=>'平台管理',
        'module'=>'应用模块',
        'server'=>'功能与服务'
    );

    function accInit($check=false){
        global $_W,$_GPC;
        $uniacid = intval($_GPC['uniacid']);
        $this->account = Account::getByUniacid($uniacid);
        $this->role = $_W['accountRole'];
        if (!empty($this->account)){
            $this->uniacid = $uniacid;
        }
        if ($check){
            if ($this->uniacid==0 || $this->account['isdeleted']==1) return error(-1, __('platformNotFound'));
            if ($this->account['endtime']>0 && $this->account['endtime']<TIMESTAMP && !$_W['isfounder']){
                return error(-1, __('platformExpired'));
            }
        }
        $_W['account'] = $this->account;
        $_W['page']['title'] = $this->account['name'];
        $_W['consolePage'] = wurl('account/profile', array('uniacid'=>$uniacid));
        return $this->account;
    }

    //平台管理控制器
    public function index(Request $request,$action='profile'){
        global $_W;
        $_W['inAccount'] = true;
        $check = in_array($action, array('component','setting'));
        $account = $this->accInit($check);
        $_W['uniacid'] = $account['uniacid'];
        if (is_error($account)){
            return $this->message($account['message'], '/console');
        }
        $method = "do".ucfirst($action);
        if (method_exists($this, $method)){
            return $this->$method($request);
        }
        return $this->message('stayTuned');
    }

    public function doRole(Request $request){
        global $_W;
        if ($this->role!='owner' && !$_W['isfounder'])return $this->message(__('暂无权限'));
        $return = array('title'=>__('操作权限'),'users'=>array(),'uniacid'=>$this->uniacid,'role'=>$this->role);
        $subs = UserService::GetSubs($_W['uid']);
        $op = $request->input('op','');
        if ($request->isMethod('post')){
            if ($op=='add') {
                $uid = (int)$request->input('uid', 0);
                if ($uid == 0) return $this->message('userNotfound');
                if (!isset($subs[$uid]) && !$_W['isfounder']) {
                    return $this->message('userNotAuthorized');
                }
                $role = (string)$request->input('role', '');
                if (!in_array($role, array('manager', 'operator'))) {
                    return $this->message('roleValid');
                }
                $complete = UserService::AccountRoleUpdate($this->uniacid, $uid, $role);
                if ($complete) return $this->message('savedSuccessfully', referer(), 'success');
            }elseif ($op=='setowner'){
                if (!$_W['isfounder']){
                    return $this->message(__('暂无权限'));
                }
                $uid = (int)$request->input('uid',0);
                if ($uid==0) return $this->message('userNotfound');
                DB::table('uni_account_users')->where(array('role'=>'owner','uniacid'=>$this->uniacid))->delete();
                $complete = UserService::AccountRoleUpdate($this->uniacid, $uid);
                if ($complete) return $this->message('savedSuccessfully', referer(), 'success');
            }
            return $this->message();
        }
        if ($op=='add'){
            $return['subusers'] = $subs;
            $return['owner'] = (int)DB::table('uni_account_users')->where(array('uniacid'=>$this->uniacid,'role'=>'owner'))->value('uid');
            return $this->globalView('console.account.roleadd',$return);
        }elseif ($op=='remove'){
            $uid = (int)$request->input('uid',0);
            if ($uid==0) return $this->message('userNotfound');
            $complete = DB::table('uni_account_users')->where(array('uid'=>$uid,'uniacid'=>$this->uniacid))->delete();
            if ($complete){
                //删除操作痕迹，待完善
                return $this->message('successful', referer(),'success');
            }
            return $this->message();
        }
        $users = DB::table('users')->leftJoin('uni_account_users','uni_account_users.uid','=','users.uid')
            ->where('uni_account_users.uniacid',$this->uniacid)
            ->whereIn('uni_account_users.role',array('owner','manager','operator'))->get()->toArray();
        if (!empty($users)){
            $roles = array('owner'=>__('owner'),'manager'=>__('manager'),'operator'=>__('operator'));
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
        $return['ownerUid'] = 0;
        $return['account'] = $this->account;
        $return['colors'] = array('owner'=>'orange','manager'=>'green','operator'=>'blue');
        return $this->globalView('console.account.role',$return);
    }

    public function doApiverify(Request $request){
        $field = 'file';
        if ($request->hasFile($field)){
            $Upload = $request->file($field);
            $ext = $Upload->getClientOriginalExtension();
            if ($ext!='txt') return $this->message(__("attachExtInvalid", ['ext'=>$ext]));
            $filename = htmlspecialchars_decode($Upload->getClientOriginalName(), ENT_QUOTES);
            $content = file_get_contents($Upload);
            $filepath = public_path($filename);
            $writer = fopen($filepath,'w');
            $complete = fwrite($writer, $content);
            fclose($writer);
            if ($complete) return $this->success('savedSuccessfully');
        }
        return $this->message();
    }

    public function doEdit(Request $request){
        if (empty($this->account)){
            return $this->message('platformNotFound');
        }
        if ($request->isMethod('post')){
            $post = $request->input('data');
            if (empty($post['name'])) return $this->message('platformNameEmpty');
            if (empty($post['logo'])) return $this->message('platformLogoEmpty');
            $post['description'] = trim($post['description']);
            $complete = Account::where('uniacid',$this->uniacid)->update($post);
            if (!$complete) return $this->message('saveFailed');
            return $this->message('savedSuccessfully',wurl('account/profile',array('uniacid'=>$this->uniacid),true), 'success');
        }
        return $this->globalView('console.account.edit',array('title'=>__('EditPlatformInformation'),'account'=>$this->account));
    }

    public function doFunctions(){
        $account = $this->account;
        $return = array('title'=>__('应用与服务'),'account'=>$account,'uniacid'=>$this->uniacid);
        $return['role'] = $this->role;
        session()->put('uniacid', $account['uniacid']);
        list($return['components'], $return['servers']) = UserService::AccountPermission($this->uniacid);

        return $this->globalView('console.account.functions',$return);
    }

    public function doModules(Request $request){
        global $_W;
        $enabled_modules = ModuleService::moduleList();
        if ($request->isMethod('post')){
            $extraModules = $request->input('extras_modules');
            $nowModules = AccountService::ExtraModules($this->uniacid, false);
            $modules = [];
            if (!empty($extraModules)){
                foreach ($extraModules as $key=>$extra){
                    if (empty($enabled_modules[$key])) continue;
                    if (empty($extra)) continue;
                    $moduleExists = $nowModules[$key];
                    $moduleInfo = array(
                        'name'=>$enabled_modules[$key]['title'],
                        'identity'=>$key,
                        'logo'=>$enabled_modules[$key]['logo'],
                        'profile'=>'default'
                    );
                    if($moduleExists['profile']=='custom'){
                        $moduleInfo['name'] = $moduleExists['name'];
                        $moduleInfo['logo'] = $moduleExists['logo'];
                        $moduleInfo['profile'] = 'custom';
                    }
                    $modules[] = $moduleInfo;
                }
            }
            DB::table('uni_account_extra_modules')->updateOrInsert(array('uniacid'=>$this->uniacid), array('modules'=>serialize($modules)));
            CacheService::flush();
            return $this->message('successful', referer(), 'success');
        }
        $return = array('title'=>__('manageData', array('data'=>__('application'))), 'modules'=>[]);
        $return['extras'] = AccountService::ExtraModules($_W['uniacid']);
        if (!empty($enabled_modules)){
            foreach ($enabled_modules as $key=>$value){
                $module = ModuleService::fetch($key);
                if (empty($module)) continue;
                $value['logo'] = tomedia($value['logo']);
                $return['modules'][$key] = $value;
            }
        }
        return $this->globalView('console.account.modules',$return);
    }

    public function doEntry(){
        global $_W, $_GPC;
        if (checksubmit()){
            $controller = trim($_GPC['ctrl']);
            if (empty($controller)) return $this->message("defaultEntryValid");
            $method = 'profile';
            if (!empty($_GPC['methods'][$controller])){
                $method = trim($_GPC['methods'][$controller]);
            }
            if (empty($method)) return $this->message("defaultEntryValid");
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
            return $this->message('savedSuccessfully', referer(), 'success');
        }
        list($controller, $method) = AccountService::GetEntrance($_W['uid'], $this->uniacid);
        $entrances = AccountService::GetAllEntrances($this->uniacid);
        return $this->globalView('console.account.entry',array(
            'title'=>__('默认入口'),
            'uniacid'=>$this->uniacid,
            'ctrl'=>$controller,
            'method'=>$method,
            'entrances'=>$entrances,
            'titles'=>$this->entrance
        ));
    }

    public function doProfile(Request $request){
        global $_W;
        $account = $this->account;
        if (empty($account) || $account['isdeleted']==1) return $this->message('platformNotFound');
        if ($account['endtime']>0 && $account['endtime']<TIMESTAMP && !$_W['isfounder']){
            return $this->message('platformExpired');
        }
        $uni_settings = DB::table('uni_settings')->where('uniacid', $this->uniacid)->select(['jsauth_acid', 'bind_domain'])->first();
        if ($request->isMethod('post')){
            $op = $request->input('op');
            switch ($op){
                case 'setDomain' : {
                    $domain = trim($request->input('domain', ''));
                    if (empty($domain) || !preg_match('/^(?:[a-zA-Z\d_-]+\.)*[a-z]{2,6}$/i', $domain)){
                        return $this->message(__('请输入正确格式的域名'));
                    }
                    if ($domain==$uni_settings['bind_domain']) return $this->success();
                    if (DB::table('uni_settings')->where('bind_domain', $domain)->exists()){
                        return $this->message(__('该域名已被其它平台绑定'));
                    }
                    if (!DB::table('uni_settings')->where('uniacid', $this->uniacid)->update(['bind_domain'=>$domain])){
                        return $this->message('saveFailed');
                    }
                    return $this->success();
                }
                case 'setExpire' : {
                    $data = array('endtime'=>0);
                    $expire = (string)$request->input('expire','');
                    if ($expire!=''){
                        $data['endtime'] = strtotime($expire." 23:59:59");
                    }
                    $complete = DB::table('account')->where('acid',$account['acid'])->update($data);
                    if (!$complete) return $this->message('saveFailed');
                    return $this->message('savedSuccessfully',wurl('account/profile',array('uniacid'=>$account['uniacid']),true), 'success');
                }
            }
        }
        $account['expirdate'] = $account['endtime']>0 ? date('Y-m-d',$account['endtime']) : __('长期');
        $return = array('title'=>__('manageData', array('data'=>__('platform'))),'account'=>$account,'uniacid'=>$this->uniacid);
        $return['role'] = $this->role;
        list($entry, $method) = AccountService::GetEntrance($_W['uid'], $this->uniacid);
        $entrances = AccountService::GetAllEntrances($this->uniacid);
        $return['entrance'] = __($this->entrance[$entry]). "&nbsp;&gt;&nbsp;";
        $return['entrance'] .= __($entrances[$entry][$method]);
        $return['settings'] = $uni_settings;
        list($return['components'], $return['servers']) = UserService::AccountPermission($this->uniacid, $_W['uid']);
        return $this->globalView('console.account.profile',$return);
    }

    public function doRemove(Request $request){
        //查询权限
        $uniacid = (int)$request->input('uniacid',0);
        if ($uniacid==0) return $this->message('selectPlatformToDelete');
        global $_W;
        $role = UserService::AccountRole($_W['uid'],$uniacid);
        if (!in_array($role,array('founder','owner')) && !$_W['isfounder']){
            return $this->message(__('暂无权限'));
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
        return $this->message('deleteSuccessfully',wurl(''),'success');
    }

    public function doCreate(Request $request){
        if ($request->isMethod('post')){
            global $_W;
            $post = $request->input('data');
            if (empty($post['name'])) return $this->message('platformNameEmpty');
            if (empty($post['logo'])) return $this->message('platformLogoEmpty');
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

                return $this->message('createSuccessfully',wurl('account/profile',array('uniacid'=>$uniacid)),'success');
            }
            return $this->message('saveFailed');
        }
        return $this->globalView('console.account.create', array('title'=>__('platformCreate')));
    }

    public function doPermission(Request $request){
        $uid=$request->input('uid');
        $uniacid=$request->input('uniacid');
        //获取权限
        $permissionInfo = DB::table('users_permission')->where(['uid'=>$uid,'uniacid'=>$this->uniacid])->first();

        //保存权限
        if ($request->isMethod('post')){
            $routesData = $request->input('routes');
            $servers = $request->input('servers', []);
            $modules = $request->input('modules', []);

            $permissionData = ['modules'=>[], 'servers'=>[]];
            foreach ($modules ?? [] as $module=>$value){
                $permissionData['modules'][$module] = $routesData['modules'][$module] ?: [];
            }
            foreach ($servers ?? [] as $server=>$value){
                $permissionData['servers'][$server] = $routesData['servers'][$server] ?: [];
            }

            $permission = serialize($permissionData);

            if(!empty($permissionInfo)){
                $res = DB::table('users_permission')->where(['id'=>$permissionInfo['id']])->update(['permission'=>$permission]);
            }else{
                $data = [
                    'uniacid'=>$uniacid,
                    'uid'=>$uid,
                    'permission'=>$permission
                ];
                $res = DB::table('users_permission')->insert($data);
            }

            if($res){
                UserService::AccountPermission($this->uniacid, $uid, false);
                $redirect = $request->input('redirect', wurl('account/profile',array('uniacid'=>$uniacid)));
                return $this->message('savedSuccessfully', $redirect,'success');
            }

            return $this->message('saveFailed');
        }
        //获取已安装应用
        $modulesList = ModuleService::moduleList();
        $permission = empty($permissionInfo['permission']) ? [] : unserialize($permissionInfo['permission']);
        $components = AccountService::ExtraModules($uniacid);

        foreach ($modulesList as $key  => &$value){
            //未添加，移除应用
            if(empty($components[$key])){
                unset($modulesList[$key]);
                continue;
            }


            $value['title'] = $components[$key]['name'];
            $value['permissions'] = unserialize($value['permissions'] ?? '');
            $value['hasPerm'] = isset($permission['modules'][$key]);
            if(empty($value['permissions'])){
                continue;
            }

            $currentPermission = $permission['modules'][$key] ?? null;
            if(empty($currentPermission)) continue;

            //比较是否已设置权限
            foreach ($value['permissions'] as &$val){
                $val['exist'] = in_array($val['route'], $currentPermission);
                $val['indeterminate'] = false;
                if (empty($val['subPerm'])){
                    $val['subPerm'] = array(
                        ['route'=>'index', 'name'=>$value['name'], 'exist'=>false]
                    );
                    continue;
                }
                $permissions = 0;
                //二级权限
                foreach ($val['subPerm'] as $k => $v){
                    $route = $val['route'] . "." . $v['route'];
                    $val['subPerm'][$k]['exist'] = in_array($route, $currentPermission);
                    if ($val['subPerm'][$k]['exist']){
                        $permissions += 1;
                    }
                }
                if ($permissions < count($val['subPerm'])){
                    $val['indeterminate'] = true;
                }
            }
            unset($val, $permissions);
        }
        unset($value);
        //读取可用服务
        $serversList = pdo_getall("microserver_unilink", array('status'=>1));
        foreach ($serversList as &$value){

            $value['perms'] = $value['perms']?unserialize($value['perms']):[];
            $value['hasPerm'] = isset($permission['servers'][$value['name']]);

            if(empty($value['perms'])){
                $value['perms']=['name'=>__('serviceEntry'),'route'=>'entrance'];
            }
            $currentPermission=$permission['servers'][$value['name']] ?? [];

            //比较是否已设置权限
            foreach ($value['perms'] as $ke => &$val){
                $val=['name'=>$val,'route'=>$ke];
                $val['exist']= in_array($val['route'],$currentPermission);

                //二级权限
                foreach ($val['subPerm'] ?? [] as $k => $v){
                    $val['subPerm'][$k]['exist']= in_array($v['route'],$currentPermission);
                }
            }
            unset($val);
        }
        unset($value);
        return $this->globalView('console.account.permission',[
            'uid'         => $uid,
            'uniacid'     => $uniacid,
            'modulesList' => $modulesList,
            'serversList'     => $serversList,
        ]);
    }

}
