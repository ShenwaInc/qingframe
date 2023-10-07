<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
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
        $return = array('title'=>__('newData', array('data'=>__('subAccount'))),'uid'=>$uid,'user'=>array('uid'=>0,'username'=>'','remark'=>'','endtime'=>0,'maxaccount'=>0));
        if ($uid>0){
            $user = DB::table('users')->where('uid',$uid)->first();
            if (empty($user)) return $this->message('userNotfound');
            if ($user['owner_uid']!=$_W['uid'] && !$_W['isfounder']){
                return $this->message('userNotAuthorized');
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
                if ($uid==0) return $this->message(__("typeSomething", array('data'=>__('username'))));
                $data['username'] = $user['username'];
            }else{
                $namelen = mb_strlen($data['username'],'utf-8');
                if ($namelen<3 || $namelen>15) return $this->message('usernameValid');
            }
            if (empty($endtime)){
                $data['endtime'] = 0;
            }else{
                $data['endtime'] = strtotime($endtime);
            }
            if (!empty($password)){
                $pwdLen = strlen($password);
                $passportLen = (int)env('APP_PASSPORT_LEN', 6);
                if ($pwdLen<$passportLen) return $this->message(__('newPasswordValid', array('len'=>$passportLen)));
                if ($uid==0){
                    if (empty($repassword)) return $this->message('reTypePassword');
                    if ($password!=$repassword) return $this->message('rePasswordError');
                }
                $data['salt'] = \Str::random(8);
                $data['password'] = sha1("{$password}-{$data['salt']}-{$_W['config']['setting']['authkey']}");
            }elseif ($uid==0){
                return $this->message('typeNewPassword');
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
            if ($complete) return $this->message('savedSuccessfully', referer(), 'success');
            return $this->message();
        }
        return $this->globalView('console.user.create',$return);
    }

    public function doRemove(Request $request){
        global $_W;
        $uid = (int)$request->input('uid',0);
        $query = DB::table('users')->where('uid',$uid);
        $user = $query->first();
        if (empty($user)) return $this->message('userNotfound');
        if ($user['owner_uid']!=$_W['uid'] && !$_W['isfounder']){
            return $this->message('userNotAuthorized');
        }
        $complete = $query->update(array('status'=>3));
        if ($complete){
            return $this->message('deleteSuccessfully',wurl('user/subuser'),'success');
        }
        return $this->message();
    }

    public function doCheckout(Request $request){
        global $_W;
        $uid = (int)$request->input('uid',0);
        $user = User::where('uid', $uid)->first();
        if (empty($user) || $user->status!=2) return $this->message('userNotfound');
        if ($user->owner_uid!=$_W['uid'] && !$_W['isfounder']){
            return $this->message('userNotAuthorized');
        }
        //清除会话
        $request->session()->flush();
        //退出登录
        Auth::logout();
        $_W['uid'] = 0;
        $_W['user'] = array('uid'=>0,'username'=>__('visitor'));
        //自动登录
        Auth::login($user, true);
        return $this->message(__('userSwitchLogin', ['name'=>$user->username]),url('console'),'success');
    }

    public function doSubuser(Request $request){
        global $_W;
        $data = array('title'=>__('manageData', array('data'=>__('subAccount'))),'users'=>array());
        $users = UserService::GetSubs($_W['uid']);
        if (!empty($users)){
            foreach ($users as &$value){
                $value['expiredate'] = __('longtime');
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
                return $this->message("attachFileInvalid");
            }
            if (DB::table('users_profile')->where('uid',$_W['uid'])->update(['avatar'=>$avatar,'edittime'=>TIMESTAMP])){
                return $this->message("savedSuccessfully",wurl('user/profile'),'success');
            }
        }
        return $this->message();
    }

    public function doProfile(Request $request){
        global $_W;
        $return = array('title'=>__('accountManagement'));
        $profile = pdo_get('users_profile',array('uid'=>$_W['uid']));
        $return['profile'] = !empty($profile) ? $profile : array(
            'avatar' => $_W['setting']['page']['logo']
        );
        return $this->globalView('console.user.profile',$return);
    }

    public function doPassport(Request $request){
        $return = array('title'=>__('loginPassword'));
        if ($request->isMethod('post')){
            global $_W;
            $passportLen = (int)env('APP_PASSPORT_LEN', 6);
            $password = $request->input('oldpassword');
            if (empty($password)) return $this->message('typeOldPassword');
            $newpassowrd = $request->input('newpassword');
            //验证旧密码
            $user = pdo_get('users',array('uid'=>$_W['uid']),array('uid','password','salt'));
            $hash = sha1("{$password}-{$user['salt']}-{$_W['config']['setting']['authkey']}");
            if ($hash!=$user['password']){
                return $this->message('validOldPassword');
            }
            if (!$newpassowrd) return $this->message('typeNewPassword');
            if ($password==$newpassowrd) return $this->message('rePasswordValid');
            $repassword = $request->input('repassword');
            if (!$repassword) return $this->message('reTypePassword');
            if ($newpassowrd!=$repassword) return $this->message('rePasswordError');
            if (strlen($newpassowrd)<$passportLen) return $this->message(__('newPasswordValid', array('len'=>$passportLen)));
            $update = array(
                'salt'=>\Str::random(8)
            );
            $update['password'] = sha1("{$newpassowrd}-{$update['salt']}-{$_W['config']['setting']['authkey']}");
            $update['register_type'] = 0;
            $complete = pdo_update('users',$update,array('uid'=>$_W['uid']));
            if ($complete){
                Auth::logout();
                $_W['uid'] = 0;
                $_W['user'] = array('uid'=>0,'username'=>__('visitor'));
                return $this->message('rePassPortSuccessfully',url('/login'),'success');
            }
            return $this->message();
        }
        return $this->globalView('console.user.passport',$return);
    }

}
