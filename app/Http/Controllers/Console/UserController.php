<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    //
    public function index(Request $request,$op='index'){
        global $_W;
        $method = "do".ucfirst($op);
        if (method_exists($this,$method)){
            return $this->$method($request);
        }
    }

    public function doProfile(Request $request){
        global $_W;
        $return = array('title'=>'账户管理');
        $profile = pdo_get('users_profile',array('uid'=>$_W['uid']));
        $return['profile'] = !empty($profile) ? $profile : array(
            'avatar' => $_W['setting']['page']['logo']
        );
        return $this->globalview('console.user.profile',$return);
    }

    public function doPassport(Request $request){
        $return = array('title'=>'登录密码');
        if ($request->isMethod('post')){
            global $_W;
            $password = $request->input('oldpassword');
            if (empty($password)) return $this->message('请输入您原来的登录密码');
            $newpassowrd = $request->input('newpassword');
            //验证旧密码
            $user = pdo_get('users',array('uid'=>$_W['uid']),array('uid','password','salt'));
            $hash = sha1("{$password}-{$user['salt']}-{$_W['config']['setting']['authkey']}");
            if ($hash!=$user['password']){
                return $this->message('旧密码不正确');
            }
            if (!$newpassowrd) return $this->message('请设置新的登录密码');
            if ($password==$newpassowrd) return $this->message('新密码不能和旧密码一样');
            $repassword = $request->input('repassword');
            if (!$repassword) return $this->message('请再次输入您的新登录密码');
            if ($newpassowrd!=$repassword) return $this->message('两次输入的密码不一致');
            if (strlen($newpassowrd)<6) return $this->message('登录密码不能少于6位数');
            $update = array(
                'salt'=>\Str::random(8)
            );
            $update['password'] = sha1("{$newpassowrd}-{$update['salt']}-{$_W['config']['setting']['authkey']}");
            $complete = pdo_update('users',$update,array('uid'=>$_W['uid']));
            if ($complete){
                Auth::logout();
                $_W['uid'] = 0;
                $_W['user'] = array('uid'=>0,'username'=>'未登录');
                return $this->message('密码重置成功，请重新登录',url('/login'),'success');
            }
            return $this->message();
        }
        return $this->globalview('console.user.passport',$return);
    }

}
