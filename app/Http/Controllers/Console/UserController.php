<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function doCreate(Request $request){
        $uid = (int)$request->input('uid',0);
        if ($uid>0){
            $user = '';
        }
    }

    public function doSubuser(Request $request){
        global $_W;
        $data = array('title'=>'子账户管理','users'=>array());
        $users = DB::table('users')->where('owner_uid',$_W['user'])->get()->toArray();
        if (!empty($users)){
            foreach ($users as &$value){
                $value['expiredate'] = '永久';
                if ($value['endtime']>0){
                    $value['expiredate'] = date('Y-m-d',$value['endtime']);
                }
                $value['createdate'] = date('Y-m-d',$value['joindate']);
            }
        }
        return $this->globalview('console.user.sub',$data);
    }

    public function doAvatar(Request $request){
        if ($request->isMethod('post')){
            global $_W;
            $path = FileService::Upload($request);
            if (is_error($path)){
                return $this->message($path['message']);
            }
            DB::table('users_profile')->where('uid',$_W['uid'])->update(['avatar'=>$path,'edittime'=>TIMESTAMP]);
            $info = array(
                'attachment' => $path,
                'url' => tomedia($path)
            );
            return $this->message($info,wurl('user/profile'),'success');
        }
        return $this->message();
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
