<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:manage {action} {--uid=0} {--username=} {--mobile=null} {--email=null} {--password=} {--owner-uid=0} {--platform-count=null} {--expire-time=null} {--remark=} {--avatar=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '系统用户（管理员）管理工具，action 可选值：create（创建用户）|update（更新资料）|delete（删除用户）|display（展示所有用户）|reset-password（重置密码）';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $action = $this->argument('action');
        $uid = (int)$this->option('uid');
        $username = trim($this->option('username'));
        $mobile = $this->option('mobile')=='null'?null:trim($this->option('mobile'));
        $email = $this->option('email')=='null'?null:trim($this->option('email'));
        $password = trim($this->option('password'));
        $ownerUid = (int)$this->option('owner-uid');
        $platformCount = (int)$this->option('platform-count');
        $expireTime = trim($this->option('expire-time'));
        $remark = trim($this->option('remark'));
        switch ($action) {
            case 'create':
                if (empty($username)){
                    $this->error(__("typeSomething", array('data'=>__('username'))));
                    return;
                }
                if (empty($password)){
                    $this->error(__("typeSomething", array('data'=>__('password'))));
                    return;
                }
                $passportLen = (int)config('system.safe.min_password_len', 6);
                if (mb_strlen($password) < $passportLen){
                    $this->error(__('newPasswordValid', array('len'=>$passportLen)));
                    return;
                }
                if (DB::table('users')->where('username', $username)->count()){
                    $this->error(__('该用户名已存在'));
                    return;
                }
                $data = array('remark'=>$remark?:'Artisan 创建于' . date('Y-m-d H:i'),'username'=>$username,'starttime'=>TIMESTAMP, 'endtime'=>0);
                if (!empty($mobile)){
                    $data['mobile'] = $mobile;
                }
                if (!empty($email)){
                    $data['email'] = $email;
                }
                if (!empty($expireTime)){
                    $data['endtime'] = $expireTime=='null' ? 0 : strtotime($expireTime . ' 23:59:59');
                }
                $data['type'] = 1;
                $data['status'] = 2;
                $data['joindate'] = TIMESTAMP;
                $data['joinip'] = '127.0.0.1';
                $data['owner_uid'] = $ownerUid ?: config('system.setting.founder', 1);
                $data['salt'] = \Str::random(8);
                $data['password'] = UserService::GetHash($password, $data['salt']);
                $uid = DB::table('users')->insertGetId($data);
                if ($uid){
                    $this->info("Create user successfully");
                    $this->info("User ID: " . $uid);
                    $this->info("User name: " . $username);
                    $this->info("User password: " . $password);
                    $avatar = $this->option('avatar')=='null'?'':$this->option('avatar');
                    if (!$avatar){
                        $avatar = '/static/icon200.jpg';
                    }
                    DB::table('users_profile')->insert(array(
                        'avatar'=>$avatar,
                        'edittime'=>TIMESTAMP,
                        'uid'=>$uid,
                        'createtime'=>TIMESTAMP,
                        'nickname'=>$data['username'],
                        'email'=>$data['email']??''
                    ));
                    DB::table('users_extra_limit')->updateOrInsert(['uid'=>$uid], [
                        'maxaccount'=>$platformCount?:0,
                        'timelimit'=>$data['endtime']?:0
                    ]);
                }else{
                    $this->error("Create user failed");
                }
                break;
            case 'update':
                $update = false;
                $condition = [];
                if (!empty($uid)){
                    $condition = ['uid'=>$uid];
                }elseif (!empty($username)){
                    $condition = ['username'=>$username];
                }
                $user = DB::table('users')->where($condition)->first();
                if (!$user){
                    $this->error(__('userNotfound'));
                    return;
                }
                $userData = [];
                if (!empty($remark)){
                    $userData['remark'] = $remark;
                }
                if ($expireTime!='null'){
                    $userData['endtime'] = strexists($expireTime, '-') ? strtotime($expireTime . ' 23:59:59') : 0;
                }
                if (!empty($userData)){
                    if (!DB::table('users')->where('uid', $user['uid'])->update($userData)){
                        $this->error(__('saveFailed'));
                        return;
                    }
                    $update = true;
                }
                $profile = [];
                if ($this->option('avatar')!='null' && $this->option('avatar')!=''){
                    $profile['avatar'] = $this->option('avatar');
                }
                if (!is_null($mobile)){
                    $profile['mobile'] = $mobile;
                }
                if (!is_null($email)){
                    $profile['email'] = $email;
                }
                if (!empty($profile)){
                    $profile['edittime'] = TIMESTAMP;
                    DB::table('users_profile')->where('uid', $user['uid'])->update($profile);
                    $update = true;
                }
                $extra = [];
                if (isset($userData['endtime'])){
                    $extra['timelimit'] = (int)$userData['endtime'];
                }
                if ($this->option('platform-count')!='null'){
                    $extra['maxaccount'] = $platformCount;
                }
                if (!empty($extra)){
                    DB::table('users_extra_limit')->updateOrInsert(['uid'=>$user['uid']], $extra);
                    $update = true;
                }
                if ($update){
                    $this->info(__('saveSuccess'));
                }else{
                    $this->info(__('saveFailed'));
                }
                break;
            default:
                $this->error('无效的操作类型！');
                break;
        }
    }
}
