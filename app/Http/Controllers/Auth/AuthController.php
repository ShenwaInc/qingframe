<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    //
    public $username = '';
    public $failed_logins = 0;
    public $failed_loginid = 0;
    public $clientip = '';

    public function Logout(Request $request){
        if($request->isMethod('post')){
            global $_W;
            Auth::logout();
            \session()->flush();
            $_W['uid'] = 0;
            $_W['user'] = array('uid'=>0,'username'=>'未登录');
            return $this->message('即将退出...',url('/login'),'success');
        }
        return $this->message();
    }

    public function Login(Request $request){
        $appSecurityEntrance = env("APP_SECURITY_ENTRANCE", "/");
        if (!empty($appSecurityEntrance) && $appSecurityEntrance!="/"){
            $securityEntrance = session()->get("securityEntrance");
            if (empty($securityEntrance)){
                abort(413, "Please log in through the secure entrance");
            }
        }
        $username = trim((string)$request->input('username'));
        $password = trim((string)$request->input('password'));
        if (empty($username) || empty($password)) return $this->message('您输入的用户名或密码不正确');
        $this->clientip = $request->getClientIp();
        $this->username = $username;
        $failed_login_query = DB::table('users_failed_login')->where('username',$username);
        $failed_login = $failed_login_query->orWhere('ip',$this->clientip)->orderByDesc('lastupdate')->first(['id','ip','count','lastupdate']);
        if (!empty($failed_login)){
            $this->failed_loginid = $failed_login['id'];
            $lastupdate = TIMESTAMP - 900;
            if ($failed_login['count']>=5 && $failed_login['lastupdate']>$lastupdate  && $failed_login['ip']==$this->clientip){
                return $this->message('您登录错误次数过多，请15分钟后再试');
            }else{
                if ($failed_login['lastupdate']<=$lastupdate || $failed_login['ip']!=$this->clientip){
                    DB::table('users_failed_login')->where('ip',$this->clientip)->delete();
                }else{
                    $this->failed_logins = $failed_login['count'];
                }
            }
        }
        $user = DB::table('users')->where('username',$username)->first(['uid','username','password','salt','remember_token','status','endtime','welcome_link']);
        if (empty($user)) $this->failed_login('找不到该用户');
        $remember = !empty($request->input('remember'));
        if (Auth::attempt(['username'=>$username,'password'=>$password], $remember)){
            Session::save();
            if ($this->failed_logins>0){
                DB::table('users_failed_login')->where('ip',$this->clientip)->delete();
            }
            DB::table('users_login_logs')->insert(array(
                'uid'=>$user['uid'],
                'ip'=>$this->clientip,
                'city'=>'',
                'createtime'=>TIMESTAMP
            ));
            $redirect = url('console');
            $uniacid = (int)$request->input('uniacid');
            if (!empty($uniacid)){
                $redirect = wurl('account/profile', array('uniacid'=>$uniacid));
            }
            return $this->message('恭喜您，登录成功', $redirect,'success');
        }
        return $this->failed_login();
    }

    public function Entry(Request $request, $uniacid){
        $account = AccountService::FetchUni($uniacid);
        if (is_error($account) || empty($account)){
            abort(404);
        }
        if (!empty($account['isdeleted'])){
            return $this->message("该平台已被删除", url('login'));
        }
        $user = $request->user();
        if (!empty($user['uid'])){
            $redirect = url("console/account", array('uniacid'=>$uniacid));
            return redirect($redirect);
        }
        if ($account['endtime']>0 && $account['endtime']<TIMESTAMP){
            return $this->message("该平台服务已到期，请联系管理员处理");
        }
        SettingService::Load();
        return $this->globalView(["auth.loginCustom{$account['uniacid']}","auth.login"], array('account'=>$account));
    }

    public function failed_login($msg='用户名或密码不正确'){
        if ($this->failed_logins>0){
            DB::table('users_failed_login')->where('id',$this->failed_loginid)->update(
                array('count'=>$this->failed_logins+1,'lastupdate'=>TIMESTAMP,'username'=>$this->username)
            );
        }else{
            DB::table('users_failed_login')->insert(array(
                'ip'=>$this->clientip,
                'username'=>$this->username,
                'count'=>1,
                'lastupdate'=>TIMESTAMP
            ));
        }
        return $this->message($msg);
    }
}
