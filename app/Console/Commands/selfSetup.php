<?php

namespace App\Console\Commands;

use App\Http\Middleware\App;
use App\Models\Account;
use App\Services\CloudService;
use App\Services\ModuleService;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class selfSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:setup {user=admin} {pwd=123456} {manual?} {appName?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'System automatic installation';

    public $defaultParams = array(
        "name"=>"轻如云应用服务管理系统",
        "aName"=>"轻如云系统",
        "logo"=>"/static/icon200.jpg",
        "icon"=>"/favicon.ico",
        "copyright"=>"© 2019-2022 Shenwa Studio. All Rights Reserved.",
        "website"=>"https://www.qingruyun.com",
        "accountName"=>"whotalk",
        "accountDescription"=>"做社交从未如此简单"
    );

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
     * @return mixed|void
     */
    public function handle()
    {
        global $_W;
        $params = $this->arguments();
        $title = $params['appName']?:$this->defaultParams['name'];
        if (!isset($_W['framework'])){
            $application = new App();
            $application->initialize(new Request());
        }
        if (file_exists(storage_path('installed.bin'))){
            $this->message("The system has been installed.");
        }
        //1.数据库迁移
        try {
            @ini_set('max_execution_time',900);
            //import database
            $this->call('migrate');
        }catch (\Exception $exception){
            return $this->message($_W['config']['debugMode']?$exception->getMessage():'Database migrate failed.');
        }
        //2.创建默认账户
        $authKey = \Str::random(12);
        $salt = \Str::random(8);
        $username = $params['user'] ?: "admin";
        $founderPWD = $params['pwd'] ?: "123456";
        $pwdHash = sha1("{$founderPWD}-{$salt}-{$authKey}");
        $founder = array(
            'groupid'=>1,
            'founder_groupid'=>1,
            'username'=>$username,    //默认账号
            'password'=>$pwdHash,
            'salt'=>$salt,
            'status'=>2,
            'joindate'=>TIMESTAMP,
            'register_type'=>1,
            'endtime'=>0
        );
        $uid = DB::table('users')->insertGetId($founder);
        if(!$uid) return $this->message('Account creation failed.');
        $_W['uid'] = $founder['uid'] = $uid;
        $_W['user'] = $founder;
        DB::table('users_profile')->insert(array(
            'avatar'=>'/web/resource/images/noavatar_middle.gif',
            'edittime'=>TIMESTAMP,
            'uid'=>$uid,
            'createtime'=>TIMESTAMP,
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
            'createtime' => TIMESTAMP,
            'create_uid' => $uid
        ));
        if (empty($uniacid)) return $this->message('System initialization failed.');
        $account_data = array('name' => $this->defaultParams['accountName']);

        $acid = Account::account_create($uniacid,$account_data);
        $uni_account->where('uniacid',$uniacid)->update(array('default_acid' => $acid));
        UserService::AccountRoleUpdate($uniacid,$uid);

        //4.初始化云服务
        DB::table('gxswa_cloud')->insert(array(
            'identity'=>$_W['config']['identity'],
            'name'=>'轻如云系统V1',
            'modulename'=>'',
            'type'=>0,
            'logo'=>'//shenwahuanan.oss-cn-shenzhen.aliyuncs.com/images/4/2021/08/pK8iHw0eQg5hHgg4Kqe5E1E1hSBpZS.png',
            'website'=>'https://www.gxswa.com/laravel/',
            'rootpath'=>'',
            'version'=>QingVersion,
            'releasedate'=>QingRelease,
            'addtime'=>TIMESTAMP,
            'dateline'=>TIMESTAMP
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
                    'links'=>'<a class="copyright-link" href="https://www.yuque.com/shenwa/qingru" target="_blank">开发文档</a><a class="copyright-link ajaxshow" href="/console/setting/market">应用市场</a><a class="copyright-link" href="https://www.gxit.org/" target="_blank">关于我们</a><a class="copyright-link" href="https://www.gxit.org/" target="_blank">提交工单</a>',
                    'keywords'=>'SaaS软件，应用市场，APP开发，微信应用，微服务，微信营销，小程序开发，模块化开发，快速开发，脚手架，Laravel模块',
                    'description'=>'轻如云系统是一个基于Laravel的跨平台快速开发框架，提供丰富的基础微服务，满足各类应用程序的快速开发需求'
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

        //6.自动安装应用
        $defaultModule = env("APP_MODULE", "whotalk");
        if (!empty($defaultModule) && file_exists(public_path("addons/$defaultModule/manifest.json"))){
            ModuleService::install($defaultModule);
        }

        //7.更新环境变量
        $manualControl = (bool)$params['manual'];
        if (!$manualControl){
            $oldKey = env("APP_AUTHKEY");
            CloudService::CloudEnv("APP_AUTHKEY=$oldKey", "APP_AUTHKEY=$authKey");
        }

        //8.写入安装文件
        $installLock = base_path('storage/installed.bin');
        $writer = fopen($installLock,'w');
        $complete = fwrite($writer,base64_encode(json_encode($this->defaultParams, 320)));
        fclose($writer);
        if(!$complete){
            return $this->message('文件写入失败，请检查storage目录权限');
        }
        if (file_exists(storage_path("defaultParams.json"))){
            @unlink(storage_path("defaultParams.json"));
        }

        $this->info('System installation completed');
        return $authKey;
    }

    /**
     * @throws \Exception
     */
    private function message(string $string){
        throw new \Exception($string);
    }
}
