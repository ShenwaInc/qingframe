<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Module;
use App\Models\UniAccountUser;
use App\Services\ModuleService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstallController extends Controller
{

    public $installer = [
        'isagree'=>0,
        'database'=>array(),
        'dbconnect'=>0,
        'authkey'=>''
    ];
    public $defaultParams = array(
        "name"=>"轻如云应用服务管理系统",
        "aName"=>"轻如云系统",
        "logo"=>"/static/icon200.jpg",
        "icon"=>"/favicon.ico",
        "copyright"=>"© 2019-2022 Shenwa Studio. All Rights Reserved.",
        "website"=>"https://www.qingruyun.com/",
        "accountName"=>"whotalk",
        "accountDescription"=>"做社交从未如此简单"
    );

    public function checkInstalled(){
        $installedfile = base_path('storage/installed.bin');
        if(file_exists($installedfile)){
            abort(404);
        }
    }

    function __construct(){
        $reset = (int)\request()->input('reset',0);
        if ($reset==1){
            Cache::forget('installer');
            $installer = $this->installer;
        }else{
            $installer = Cache::get('installer',$this->installer);
        }
        if (empty($installer['database'])){
            $DBConfig = config('database');
            $installer['database'] = $DBConfig['connections'][$DBConfig['default']];
        }
        $this->installer = $installer;
        if (file_exists(storage_path("defaultParams.json"))){
            $JSON = file_get_contents(storage_path("defaultParams.json"));
            $defaultParams = (array)json_decode($JSON, true);
            if (!empty($defaultParams)){
                $this->defaultParams = array_merge($this->defaultParams, $defaultParams);
            }
        }
    }

    //
    public function index(){
        $this->checkInstalled();
        if ($this->installer['isagree']){
            return redirect()->action('installController@database');
        }
        return view('install.index', $this->defaultParams);
    }

    public function install(Request $request){
        $this->checkInstalled();
        global $_W;
        if (!$request->isMethod('post')){
            return $this->message('安装失败，请重试');
        }
        if (!$this->installer['isagree']){
            return $this->message('请先同意安装协议',url('installer'));
        }
        if (isset($this->installer['database']['unix_socket'])){
            return $this->message('数据库未配置',url('installer/database'));
        }
        //写入数据库
        $DBConnect = $this->dbConnect($this->installer['database']);
        if ($DBConnect===false){
            return $this->message('数据库连接失败，请检查配置信息是否正确');
        }
        $installer = $this->installer;
        $authKey = \Str::random(12);
        $uid = 0;
        if ($installer['dbconnect']==0){
            //全新安装
            $manager = $request->input('render');
            $appName = !empty($manager['appName']) ? trim($manager['appName']) : $this->defaultParams['aName'];
            if (!isset($manager['username']) || trim($manager['username'])==''){
                return $this->message('请填写您的超管账号');
            }
            if (!isset($manager['password']) || trim($manager['password'])==''){
                return $this->message('请设置超管的登录密码');
            }

            $DBConfig = config('database');
            $databaseCFG = $DBConfig['connections'][$DBConfig['default']];
            foreach ($databaseCFG as $key=>$cfg){
                if(!isset($installer['database'][$key])) continue;
                $databaseCFG[$key] = $installer['database'][$key];
            }
            $databaseCFG['strict'] = false;
            Config::set('database.connections.'.$DBConfig['default'],$databaseCFG);

            try {
                @ini_set('max_execution_time',900);
                //import database
                Artisan::call('migrate');
            }catch (\Exception $exception){
                return $this->message('数据库安装失败');
            }

            $salt = \Str::random(8);
            $founderPWD = trim($manager['password']);
            $pwdHash = sha1("{$founderPWD}-{$salt}-{$authKey}");
            $founder = array(
                'groupid'=>1,
                'founder_groupid'=>1,
                'username'=>trim($manager['username']),
                'password'=>$pwdHash,
                'salt'=>$salt,
                'status'=>2,
                'joindate'=>TIMESTAMP,
                'endtime'=>0
            );
            //create founder
            $uid = DB::table('users')->insertGetId($founder);
            if(!$uid) return $this->message('数据写入失败');
            $_W['uid'] = $founder['uid'] = $uid;
            $_W['user'] = $founder;
            DB::table('users_profile')->insert(array(
                'avatar'=>'/static/icon200.jpg',
                'edittime'=>TIMESTAMP,
                'uid'=>$uid,
                'createtime'=>TIMESTAMP,
                'nickname'=>$founder['username']
            ));
            //create account
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
            if (empty($uniacid)) return $this->message('系统初始化失败');
            $account_data = array('name' => $this->defaultParams['accountName']);

            $acid = Account::account_create($uniacid,$account_data);
            $uni_account->where('uniacid',$uniacid)->update(array('default_acid' => $acid));
            UserService::AccountRoleUpdate($uniacid,$uid);

            //initializer laravel framework
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

            //initializer default setting
            DB::table("core_settings")->insert([
                array(
                    'key'=>"page",
                    'value'=>serialize(array(
                        'title'=>$appName,
                        'icon'=>$this->defaultParams['icon'],
                        'logo'=>$this->defaultParams['logo'],
                        'copyright'=>$this->defaultParams['copyright'],
                        'links'=>'<a class="copyright-link" href="https://www.yuque.com/shenwa/qingru" target="_blank">开发文档</a><a class="copyright-link ajaxshow" href="/console/setting/market">应用市场</a><a class="copyright-link" href="https://www.gxit.org/" target="_blank">关于我们</a><a class="copyright-link" href="https://www.gxit.org/" target="_blank">提交工单</a>',
                        'keywords'=>'SaaS软件，应用市场，APP开发，微信应用，微服务，微信营销，小程序开发，模块化开发，快速开发，脚手架，Laravel模块',
                        'description'=>'轻如云系统是一个基于Laravel的跨平台快速开发框架，提供丰富的基础微服务，满足各类应用程序的快速开发需求'
                    ))
                )
            ]);
        }else{
            //安装到现有微擎，待完善
        }
        //写入配置文件
        $envfile_tmp = resource_path('stub/env.stub');
        $reader = fopen($envfile_tmp,'r');
        $envData = fread($reader,filesize($envfile_tmp));
        fclose($reader);
        $baseurl = str_replace('/installer/render','',url()->current());
        $database = $installer['database'];
        $searches = array(
            '{AUTHKEY}',
            '{APP_DEBUG}',
            '{BASEURL}',
            '{FOUNDER}',
            '{APP_VERSION}',
            '{APP_RELEASE}',
            '{DB_HOST}',
            '{DB_PORT}',
            '{DB_DATABASE}',
            '{DB_USERNAME}',
            '{DB_PASSWORD}',
            '{DB_PREFIX}',
            '{SESSION_DRIVER}',
            '{REDIS_HOST}',
            '{REDIS_PASSWORD}',
            '{REDIS_PORT}'
        );
        $replaces = array(
            $authKey,
            \config('app.debug', false) ? 'true' : 'false',
            $baseurl,
            $uid,
            QingVersion,
            QingRelease,
            $database['host'],
            $database['port'],
            $database['database'],
            $database['username'],
            $database['password'],
            $database['prefix'],
            \config('session.driver', 'file'),
            env('REDIS_HOST','127.0.0.1'),
            env('REDIS_PASSWORD', 'null'),
            env('REDIS_PORT','6379')
        );
        $envData = str_replace($searches, $replaces, $envData);
        $envFile = base_path(".env");
        if (file_exists($envFile)){
            @unlink($envFile);
        }
        $writer = fopen($envFile,'w');
        if(!fwrite($writer,$envData)){
            fclose($writer);
            return $this->message('文件写入失败，请检查根目录权限');
        }
        fclose($writer);
        //写入安装文件
        $installLock = base_path('storage/installed.bin');
        $writer = fopen($installLock,'w');
        $installer['baseurl'] = $baseurl;
        $installer['authkey'] = $authKey;
        unset($installer['database']);
        $complete = fwrite($writer,base64_encode(json_encode($installer, 320)));
        fclose($writer);
        if(!$complete){
            return $this->message('文件写入失败，请检查storage目录权限');
        }
        try {
            //创建文件符号链接
            Artisan::call('storage:link');
            Artisan::call('key:generate');
            $defaultModule = env("APP_MODULE", "whotalk");
            if (!empty($defaultModule) && file_exists(public_path("addons/$defaultModule/manifest.json"))){
                ModuleService::install($defaultModule);
            }
        }catch (\Exception $exception){
            //创建文件映射失败
            Log::error('storage_link_fail',array('errno'=>-1,'message'=>$exception->getMessage()));
        }
        Cache::forget('installer');
        return $this->message('恭喜您，安装成功！','','success');
    }

    public function agreement(){
        $this->checkInstalled();
        $isagree = (int)\request('isagree');
        $this->installer['isagree'] = $isagree;
        if (!Cache::put('installer',$this->installer,7200)){
            return $this->message();
        }
        return $this->message('操作成功','','success');
    }

    public function database(){
        $this->checkInstalled();
        if (!$this->installer['isagree']){
            return redirect()->action('installController@index');
        }
        return view('install.database',$this->installer);
    }

    public function dbDetect(Request $request){
        $this->checkInstalled();
        if ($request->isMethod('post')) {
            $DBConfig = $request->input('dbconfig');
            if (empty($DBConfig)) return $this->message();
            $DBConnect = intval($DBConfig['dbconnect']);
            $authKey = trim($DBConfig['authkey']);
            $founderPWD = trim($DBConfig['founderpwd']);
            $database = array(
                'driver'=>'mysql',
                'host'=>trim($DBConfig['db[host']),
                'port'=>intval($DBConfig['db[port']),
                'database'=>trim($DBConfig['db[database']),
                'username'=>trim($DBConfig['db[username']),
                'password'=>trim($DBConfig['db[password']),
                'prefix'=>trim($DBConfig['db[prefix'])
            );
            if ($DBConnect==1){
                if (empty($founderPWD)){
                    return $this->message('创始人登录密码不能为空');
                }
                if (empty($authKey)){
                    return $this->message('微擎站点安全码不能为空');
                }
                $database['prefix'] = 'ims_';
            }
            $isConnect = $this->dbConnect($database);
            if ($isConnect===false){
                return $this->message('数据库连接失败，请检查配置信息是否正确');
            }
            if ($DBConnect==1){
                try {
                    $founder = $isConnect->table('users')->select('uid','password','salt')->where('founder_groupid',1)->orderBy('uid','asc')->first();
                }catch (\Exception $e){
                    if (!empty($e->errorInfo) && $e->errorInfo[0]=='42S02'){
                        //Table dosn't exist
                        return $this->message('非微擎站点数据库');
                    }
                    return $this->message('数据库连接异常');
                }
                if (isset($founder->uid)){
                    $pwdHash = sha1("{$founderPWD}-{$founder->salt}-{$authKey}");
                    if ($pwdHash!=$founder->password){
                        return $this->message('创始人密码或安全码不正确');
                    }
                }else{
                    return $this->message('该创始人不存在');
                }
            }else{
                try {
                    $accounts = $isConnect->table('account')->count();
                }catch (\Exception $e){
                    //Todo something
                    unset($accounts);
                }
                if (isset($accounts)){
                    //Table exist
                    return $this->message('该数据库已经安装过'.$this->defaultParams['aName'].'或其它同类产品');
                }
            }
            $this->installer['dbconnect'] = $DBConnect;
            $this->installer['database'] = $database;
            $this->installer['authkey'] = $authKey;
            Cache::forget('installer');
            if (!Cache::put('installer',$this->installer,7200)){
                return $this->message();
            }
            return $this->message('操作成功','','success');
        }
        return $this->message();
    }

    public function render(){
        $this->checkInstalled();
        if (!$this->installer['isagree']){
            return redirect()->action('installController@index');
        }
        if (isset($this->installer['database']['unix_socket'])){
            return redirect()->action('installController@database');
        }
        $data = $this->installer;
        $data['appName'] = $this->defaultParams['aName'];
        return view('install.render', $data);
    }

    /**
     * @return object|boolean|int|mixed|null
    */
    public function dbConnect($database=array()){
        if(empty($database['host']) || empty($database['database']) || empty($database['username']) || empty($database['password'])) return false;
        if (!$database['port']) $database['port'] = 3306;
        $database['strict'] = false;
        $capsule = new Capsule();
        $capsule->addConnection($database,'mysqldetect');
        $capsule->bootEloquent();
        try {
            $conn = $capsule->getConnection('mysqldetect');
            //$conn->raw()
            return $conn;
        } catch (\Exception $e){
            //if (empty($e->errorInfo)) return false;
            return false;//@json_decode(json_encode($e->errorInfo),true);
        }
    }

}
