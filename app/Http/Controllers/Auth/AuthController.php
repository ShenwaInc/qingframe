<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public $username = '';
    public $failed_logins = 0;
    public $clientip = '';

    public function Login(Request $request){
        global $_W;
        $username = trim((string)$request->input('username'));
        $password = trim((string)$request->input('password'));
        if (empty($username) || empty($password)) $this->message('您输入的用户名或密码不正确');
        $this->clientip = $request->getClientIp();
        $this->username = $username;
        $failed_login = DB::table('users_failed_login')->where('username',$username)->first(['ip','lastupdate','count']);
        if (!empty($failed_login)){
            $lastupdate = TIMESTAMP - 900;
            if ($failed_login['count']>=5 && $failed_login['lastupdate']>$lastupdate  && $failed_login['ip']==$this->clientip){
                $this->message('您登录错误次数过多，请15分钟后再试');
            }else{
                if ($failed_login['lastupdate']<=$lastupdate || $failed_login['ip']!=$this->clientip){
                    DB::table('users_failed_login')->where('username',$username)->delete();
                }else{
                    $this->failed_logins = $failed_login['count'];
                }
            }
        }
        $user = DB::table('users')->where('username',$username)->first(['uid','username','password','salt','remember_token','status','endtime','welcome_link']);
        if (empty($user)) $this->failed_login('找不到该用户');
        $passwordhash = sha1("{$password}-{$user['salt']}-{$_W['config']['setting']['authkey']}");
        if ($passwordhash==$user['password']){
            //login
            $LoginUser = User::where(['username'=>$username,'password'=>$passwordhash])->first();
            $LoginUser->username = $user['username'];
            $LoginUser->lastvisit = TIMESTAMP;
            $LoginUser->lastip = $this->clientip;
            $remember = !empty($request->input('remember'));
            if (!Auth::attempt(['username'=>$username,'password'=>$password], $remember)){
                $this->message('登录失败，请重试','','success');
            }
            if ($this->failed_logins>0){
                DB::table('users_failed_login')->where('username',$username)->delete();
            }
            DB::table('users_login_logs')->insert(array(
                'uid'=>$user['uid'],
                'ip'=>$this->clientip,
                'city'=>'',
                'createtime'=>TIMESTAMP
            ));
            $this->message('登录成功','','success');
        }
        $this->failed_login("密码错误");
    }

    public function failed_login($msg='您输入的用户名或密码不正确'){
        if ($this->failed_logins>0){
            DB::table('users_failed_login')->where('username',$this->username)->update(
                array('count'=>$this->failed_logins+1,'lastupdate'=>TIMESTAMP)
            );
        }else{
            DB::table('users_failed_login')->insert(array(
                'ip'=>$this->clientip,
                'username'=>$this->username,
                'count'=>1,
                'lastupdate'=>TIMESTAMP
            ));
        }
        $this->message($msg);
    }
}
