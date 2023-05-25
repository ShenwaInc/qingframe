<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\FileService;
use App\Services\UserService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //
    public function index(Request $request,$op='profile'){
        $method = "do".ucfirst($op);
        if (!method_exists($this,$method)){
            return $this->message();
        }
        return $this->$method($request);
    }

    public function doCreate(Request $request){
        global $_W;
        $uid = (int)$request->input('uid',0);
        $return = array('title'=>'创建子账户','uid'=>$uid,'user'=>array('uid'=>0,'username'=>'','remark'=>'','endtime'=>0,'maxaccount'=>0));
        if ($uid>0){
            $user = DB::table('users')->where('uid',$uid)->first();
            if (empty($user)) return $this->message('找不到该用户，可能已被删除');
            if ($user['owner_uid']!=$_W['uid'] && !$_W['isfounder']){
                return $this->message('您暂时无权操作该用户');
            }
            $user['maxaccount'] = (int)DB::table('users_extra_limit')->where('uid',$user['uid'])->value('maxaccount');
            $return['user'] = $user;
        }
        if ($request->isMethod('post')){
            $username = (string)$request->input('username','');
            $password = (string)$request->input('password','');
            $repassword = (string)$request->input('repassword','');
            $endtime = (string)$request->input('endtime','');
            $remark = (string)$request->input('remark','');
            $maxaccount = (int)$request->input('maxaccount',0);
            $data = array('remark'=>$remark,'username'=>$username,'starttime'=>TIMESTAMP);
            if (empty($data['username'])){
                if ($uid==0) return $this->message('用户名不能为空');
                $data['username'] = $user['username'];
            }else{
                $namelen = mb_strlen($data['username'],'utf-8');
                if ($namelen<3 || $namelen>15) return $this->message('用户名长度不正确(3~15)');
            }
            if (empty($endtime)){
                $data['endtime'] = 0;
            }else{
                $data['endtime'] = strtotime($endtime);
            }
            if (!empty($password)){
                $pwdlen = strlen($password);
                if ($pwdlen<6) return $this->message('密码长度不能小于6');
                if ($uid==0){
                    if (empty($repassword)) return $this->message('请确认您输入的密码');
                    if ($password!=$repassword) return $this->message('两次输入的密码不一致');
                }
                $data['salt'] = \Str::random(8);
                $data['password'] = sha1("{$password}-{$data['salt']}-{$_W['config']['setting']['authkey']}");
            }elseif ($uid==0){
                return $this->message('请设置一个登录密码');
            }
            if ($uid>0){
                $complete = DB::table('users')->where('uid',$user['uid'])->update($data);
                if ($maxaccount!=$user['maxaccount']){
                    DB::table('users_extra_limit')->updateOrInsert(array('uid'=>$user['uid']),array('maxaccount'=>$maxaccount,'timelimit'=>$data['endtime']));
                }
            }else{
                $data['type'] = 1;
                $data['status'] = 2;
                $data['joindate'] = TIMESTAMP;
                $data['joinip'] = $_W['clientip'];
                $data['owner_uid'] = $_W['uid'];
                $complete = DB::table('users')->insertGetId($data);
                if ($complete){
                    DB::table('users_profile')->insert(array(
                        'avatar'=>'/static/icon200.jpg',
                        'edittime'=>TIMESTAMP,
                        'uid'=>$complete,
                        'createtime'=>TIMESTAMP,
                        'nickname'=>$data['username']
                    ));
                    DB::table('users_extra_limit')->insert(array('uid'=>$complete,'maxaccount'=>$maxaccount,'timelimit'=>$data['endtime']));
                }
            }
            if ($complete) return $this->message('保存成功！', referer(), 'success');
            return $this->message();
        }
        return $this->globalView('console.user.create',$return);
    }

    public function doRemove(Request $request){
        global $_W;
        $uid = (int)$request->input('uid',0);
        $query = DB::table('users')->where('uid',$uid);
        $user = $query->first();
        if (empty($user)) return $this->message('找不到该用户，可能已被删除');
        if ($user['owner_uid']!=$_W['uid'] && !$_W['isfounder']){
            return $this->message('您暂时无权操作该用户');
        }
        $complete = $query->update(array('status'=>3));
        if ($complete){
            return $this->message('删除成功！',wurl('user/subuser'),'success');
        }
        return $this->message();
    }

    public function doCheckout(Request $request){
        global $_W;
        $uid = (int)$request->input('uid',0);
        $user = User::where('uid',intval($uid))->first();
        if (empty($user) || $user->status!=2) return $this->message('找不到该用户，可能已被删除');
        if ($user->owner_uid!=$_W['uid'] && !$_W['isfounder']){
            return $this->message('您暂时无权操作该用户');
        }
        //清除会话
        $request->session()->flush();
        //退出登录
        Auth::logout();
        $_W['uid'] = 0;
        $_W['user'] = array('uid'=>0,'username'=>'未登录');
        //自动登录
        Auth::login($user, true);
        return $this->message("即将切换到".$user->username."登录",url('console'),'success');
    }

    public function doSubuser(Request $request){
        global $_W;
        $data = array('title'=>'子账户管理','users'=>array());
        $users = UserService::GetSubs($_W['uid']);
        if (!empty($users)){
            foreach ($users as &$value){
                $value['expiredate'] = '永久';
                $value['expire'] = false;
                if ($value['endtime']>0){
                    $value['expiredate'] = date('Y-m-d',$value['endtime']);
                    if ($value['endtime']<=TIMESTAMP){
                        $value['expire'] = true;
                    }
                }
                $value['createdate'] = date('Y-m-d',$value['joindate']);
            }
            $data['users'] = $users;
        }
        return $this->globalView('console.user.sub',$data);
    }

    public function doAvatar(Request $request){
        if ($request->isMethod('post')){
            global $_W;
            $upload = serv('storage')->putFile('file');
            if (is_error($upload)){
                return $this->message($upload['message']);
            }
            DB::table('users_profile')->where('uid',$_W['uid'])->update(['avatar'=>$upload['path'],'edittime'=>TIMESTAMP]);
            $info = array(
                'attachment' => $upload['path'],
                'url' => $upload['url']
            );
            return $this->message($info,wurl('user/profile'),'success');
        }
        return $this->message();
    }

    public function doSetAvatar(Request $request){
        if ($request->isMethod('post')){
            global $_W;
            $avatar = $request->input('path', '');
            if (empty($avatar)){
                return $this->message("无效的图片路径");
            }
            if (DB::table('users_profile')->where('uid',$_W['uid'])->update(['avatar'=>$avatar,'edittime'=>TIMESTAMP])){
                return $this->message("更新成功！",wurl('user/profile'),'success');
            }
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
        return $this->globalView('console.user.profile',$return);
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
        return $this->globalView('console.user.passport',$return);
    }

}
