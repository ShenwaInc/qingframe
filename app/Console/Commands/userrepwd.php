<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class userrepwd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:repwd {user} {pwd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Users PassWord';

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
     * 强制重置后台管理员登录密码.
     *
     * @return mixed|null
     */
    public function handle()
    {
        //
        $username = $this->argument('user');
        if (empty($username)) return $this->error("Username cannot be empty.") || false;
        $password = $this->argument('pwd');
        if (empty($password)) return $this->error("Password cannot be empty") || false;
        $user = DB::table('users')->where('username',trim($username))->select('uid','salt','password')->first();
        if (empty($user)) return $this->error("User {$user} not found.") || false;
        $salt = \Str::random(8);
        $setting = config('system');
        $hash = UserService::GetHash(trim($password), $salt, $setting['setting']['authkey']);
        $complete = DB::table('users')->where('uid',$user['uid'])->update(array(
            'salt'=>$salt,
            'password'=>$hash
        ));
        if (!$complete) return $this->error("Password reset failed.") || false;
        $this->info('User password reset successfully.');
    }
}
