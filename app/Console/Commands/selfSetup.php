<?php

namespace App\Console\Commands;

use App\Models\UniAccount;
use App\Services\CloudService;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class selfSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:setup {user=admin} {pwd=123456} {appName?} {--authKey=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System automatic installation';

    public $defaultParams = array(
        "name"=>"轻如云开放平台",
        "aName"=>"轻如云系统",
        "logo"=>"/static/icon200.jpg",
        "icon"=>"/favicon.ico",
        "copyright"=>"© 2019-2022 ShenWa Studio. All Rights Reserved.",
        "website"=>"https://www.qingruyun.com",
        "accountName"=>"轻如云",
        "accountDescription"=>"开放连接万事万物",
        "accountId"=>0
    );
    public $definedSTDIN = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if (file_exists(storage_path("defaultParams.json"))){
            $JSON = file_get_contents(storage_path("defaultParams.json"));
            $defaultParams = (array)json_decode($JSON, true);
            if (!empty($defaultParams)){
                $this->defaultParams = array_merge($this->defaultParams, $defaultParams);
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return array|bool|string|void|null
     * @throws \Exception
     */
    public function handle()
    {
        global $_W;
        $_W['TerminalSilence'] = 1;
        $params = $this->arguments();
        $title = $params['appName']?:$this->defaultParams['name'];
        if (file_exists(storage_path('installed.bin'))){
            $this->message("The system has been installed.");
        }
        //1.数据库迁移
        try {
            $this->call('config:clear');

            $databaseCFG = config('database.connections.mysql');
            if ($databaseCFG['username']=='forge'){
                $installer = Cache::get('installer');
                if (!empty($installer['database'])){
                    foreach ($databaseCFG as $key=>$cfg){
                        if(!isset($installer['database'][$key])) continue;
                        $databaseCFG[$key] = $installer['database'][$key];
                    }
                    $databaseCFG['strict'] = false;
                    Config::set('database.default', 'mysql');
                    app('config')->set('database.default', 'mysql');
                    Config::set('database.connections.mysql',$databaseCFG);
                    app('config')->set('database.connections.mysql',$databaseCFG);
                }else{
                    $this->error("Undefined database configure.");
                    return;
                }
            }

            $db = app(DatabaseManager::class);
            $db->purge('mysql');

            @ini_set('max_execution_time',900);
            //import database
            if (!defined('STDIN')) {
                define('STDIN', fopen('php://stdin', 'r'));
                $this->definedSTDIN = true;
            }
            $this->call('migrate', ['--force' => true]);
        }catch (\Exception $exception){
            return $this->message($_W['config']['debugMode']?$exception->getMessage():'Database migrate failed.(' . \config('database.connections.mysql.username').')');
        }
        //2.创建默认账户
        $authKey = $this->option("authKey");
        if (empty($authKey) || $authKey=="default"){
            $authKey = Str::random(12);
        }
        $salt = Str::random(8);
        $username = $params['user'] ?: "admin";
        $founderPWD = $params['pwd'] ?: "123456";
        $pwdHash = sha1("$founderPWD-$salt-$authKey");
        $register_type = 0;
        if ($username=='admin' && $founderPWD=='123456'){
            $register_type = 1;
        }
        $founder = array(
            'groupid'=>1,
            'founder_groupid'=>1,
            'username'=>$username,    //默认账号
            'password'=>$pwdHash,
            'salt'=>$salt,
            'status'=>2,
            'joindate'=>time(),
            'register_type'=>$register_type,
            'endtime'=>0
        );
        $uid = DB::table('users')->insertGetId($founder);
        if(!$uid) return $this->message('Account creation failed.');
        $_W['uid'] = $founder['uid'] = $uid;
        $_W['user'] = $founder;
        DB::table('users_profile')->insert(array(
            'avatar'=>'/web/resource/images/noavatar_middle.gif',
            'edittime'=>time(),
            'uid'=>$uid,
            'createtime'=>time(),
            'nickname'=>$founder['username']
        ));
        //3.创建默认平台
        $uni_account = DB::table('uni_account');
        $uniacid = $uni_account->insertGetId(array(
            'groupid' => 0,
            'default_acid' => 0,
            'name' => $this->defaultParams['accountName'],
            'description' => $this->defaultParams['accountDescription'],
            'logo'=>$this->defaultParams['logo'],
            'title_initial' => 'W',
            'createtime' => time(),
            'create_uid' => $uid
        ));
        if (empty($uniacid)) return $this->message('System initialization failed.');
        $account_data = array('name' => $this->defaultParams['accountName']);

        $acid = UniAccount::account_create($uniacid,$account_data);
        $uni_account->where('uniacid',$uniacid)->update(array('default_acid' => $acid));
        UserService::AccountRoleUpdate($uniacid,$uid);

        //4.初始化云服务
        DB::table('gxswa_cloud')->insert(array(
            'identity'=>env('APP_IDENTITY', 'swa_framework_laravel'),
            'name'=>'轻如云系统V1',
            'modulename'=>'',
            'type'=>0,
            'logo'=>'//shenwahuanan.oss-cn-shenzhen.aliyuncs.com/images/4/2021/08/pK8iHw0eQg5hHgg4Kqe5E1E1hSBpZS.png',
            'website'=>'https://www.gxswa.com/laravel/',
            'rootpath'=>'',
            'version'=>env('APP_VERSION'),
            'version_code'=>(int)env('APP_RELEASE'),
            'addtime'=>time(),
            'dateline'=>time()
        ));

        //5.初始化默认设置
        DB::table("core_settings")->insert([
            array(
                'key'=>"page",
                'value'=>serialize(array(
                    'title'=>$title,
                    'icon'=>$this->defaultParams['icon'],
                    'logo'=>$this->defaultParams['logo'],
                    'copyright'=>$this->defaultParams['copyright'],
                    'links'=>'<a class="copyright-link" href="https://www.yuque.com/shenwa/qingru" target="_blank">开发文档</a><a class="copyright-link ajaxshow" href="/console/setting/market">应用市场</a><a class="copyright-link" href="https://www.gxit.org/" target="_blank">关于我们</a><a class="copyright-link ajaxshow" href="/console/report/post">提交工单</a>',
                    'keywords'=>'SaaS软件，应用市场，APP开发，微信应用，微服务，微信营销，小程序开发，模块化开发，快速开发，脚手架，Laravel模块',
                    'description'=>'轻如云系统是一款多租户（SaaS）、模块化的WEB系统集成开放平台'
                ))
            )
        ]);
        try {
            $this->call('storage:link');
            $this->call('key:generate');
        }catch (\Exception $exception){
            //创建文件映射失败
            Log::error('storage_link_fail',array('errno'=>-1,'message'=>$exception->getMessage()));
        }

        //6.更新环境变量
        if (!empty($authKey)){
            $oldKey = env("APP_AUTHKEY");
            CloudService::CloudEnv("APP_AUTHKEY=$oldKey", "APP_AUTHKEY=$authKey");
        }

        //7.写入安装文件
        $installLock = base_path('storage/installed.bin');
        $writer = fopen($installLock,'w');
        $complete = fwrite($writer,base64_encode(json_encode($this->defaultParams, 320)));
        fclose($writer);
        if(!$complete){
            return $this->message('文件写入失败，请检查storage目录权限');
        }
        if (file_exists(storage_path("defaultParams.json")) && !DEVELOPMENT){
            @unlink(storage_path("defaultParams.json"));
        }

        //8.自动安装应用（取消该步骤，调整为激活后安装，避免微服务安装授权验证失败）

        //9.指定唯一平台
        if (!empty($this->defaultParams['accountId'])){
            CloudService::CloudEnv("APP_UNIACID=0", "APP_UNIACID=$uniacid");
        }

        $this->info('System installation completed');
        $this->info('Please access the console via your domain name');
        $this->info("username: $username");
        $this->info("password: $founderPWD");
        return $authKey;
    }

    /**
     * @throws \Exception
     */
    private function message(string $string){
        if($this->definedSTDIN && defined('STDIN')){
            fclose(STDIN);
        }
        throw new \Exception($string);
    }
}
