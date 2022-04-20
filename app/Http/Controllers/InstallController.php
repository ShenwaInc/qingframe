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

    public $installer = ['isagree'=>0,'database'=>array(),'dbconnect'=>0,'authkey'=>'','socket'=>[]];
    public $defaultsocket = array(
        'type'=>'remote',
        'server'=>'wss://socket.whotalk.com.cn/wss',
        'webapi'=>'https://socket.whotalk.com.cn/api/message/sendMessageToUser'
    );

    function __construct(){
        $installedfile = base_path('storage/installed.bin');
        if(file_exists($installedfile)){
            abort(404);
            die();
        }
        $reset = (int)\request()->input('reset',0);
        if ($reset==1){
            Cache::forget('installer');
            $installer = $this->installer;
        }else{
            $installer = Cache::get('installer',$this->installer);
        }
        if (empty($installer['database'])){
            $dbconfig = config('database');
            $installer['database'] = $dbconfig['connections'][$dbconfig['default']];
        }
        if (empty($installer['socket'])){
            $installer['socket'] = $this->defaultsocket;
        }
        $this->installer = $installer;
    }

    //
    public function index(){
        if ($this->installer['isagree']){
            return redirect()->action('installController@database');
        }
        return view('install.index');
    }

    public function install(Request $request){
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
        $dbconnect = $this->dbConnect($this->installer['database']);
        if ($dbconnect===false){
            return $this->message('数据库连接失败，请检查配置信息是否正确');
        }
        $installer = $this->installer;
        $authkey = $installer['authkey'];
        if ($installer['dbconnect']==0){
            $authkey = \Str::random(8);
            $manager = $request->input('render');
            if (!isset($manager['username']) || trim($manager['username'])==''){
                return $this->message('请填写您的超管账号');
            }
            if (!isset($manager['password']) || trim($manager['password'])==''){
                return $this->message('请设置超管的登录密码');
            }

            $dbconfig = config('database');
            $databasecfg = $dbconfig['connections'][$dbconfig['default']];
            foreach ($databasecfg as $key=>$cfg){
                if(!isset($installer['database'][$key])) continue;
                $databasecfg[$key] = $installer['database'][$key];
            }
            $databasecfg['strict'] = false;
            Config::set('database.connections.'.$dbconfig['default'],$databasecfg);

            try {
                @ini_set('max_execution_time',900);
                //import database
                Artisan::call('migrate');
            }catch (\Exception $exception){
                return $this->message('数据库安装失败');
            }

            //initialize modules
            try {
                //import database
                ModuleService::Initializer();
            }catch (\Exception $exception){
                return $this->message('初始化数据失败');
            }

            $salt = \Str::random(8);
            $founderpwd = trim($manager['password']);
            $pwdhash = sha1("{$founderpwd}-{$salt}-{$authkey}");
            $founder = array(
                'groupid'=>1,
                'founder_groupid'=>1,
                'username'=>trim($manager['username']),
                'password'=>$pwdhash,
                'salt'=>$salt,
                'status'=>2,
                'joindate'=>TIMESTAMP,
                'endtime'=>0
            );
            //create founder
            $uid =  (int)DB::table('users')->insertGetId($founder);
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
            $post = array('name'=>'Whotalk','description'=>'做社交从未如此简单');
            $uni_account = DB::table('uni_account');
            $uniacid = $uni_account->insertGetId(array(
                'groupid' => 0,
                'default_acid' => 0,
                'name' => $post['name'],
                'description' => $post['description'],
                'logo'=>'/static/icon200.jpg',
                'title_initial' => 'W',
                'createtime' => TIMESTAMP,
                'create_uid' => $uid
            ));
            if (empty($uniacid)) return $this->message('系统初始化失败');
            $account_data = array('name' => $post['name']);

            $acid = Account::account_create($uniacid,$account_data);
            $uni_account->where('uniacid',$uniacid)->update(array('default_acid' => $acid));
            UserService::AccountRoleUpdate($uniacid,$uid);

            //initialize mc group
            DB::table('mc_groups')->insert(array('uniacid' => $uniacid, 'title' => '默认会员组', 'isdefault' => 1));

            //initialize uni setting
            DB::table('uni_settings')->insert(array(
                'creditnames' => serialize(array('credit1' => array('title' => '积分', 'enabled' => 1), 'credit2' => array('title' => '余额', 'enabled' => 1))),
                'creditbehaviors' => serialize(array('activity' => 'credit1', 'currency' => 'credit2')),
                'uniacid' => $uniacid,
                'default_site' => 0,
                'sync' => serialize(array('switch' => 0, 'acid' => '')),
            ));

            //initializer laravel framework
            $config = \config('system');
            DB::table('gxswa_cloud')->insert(array(
                'identity'=>'swa_framework_laravel',
                'name'=>'轻如框架V1',
                'modulename'=>'',
                'type'=>0,
                'logo'=>'//shenwahuanan.oss-cn-shenzhen.aliyuncs.com/images/4/2021/08/pK8iHw0eQg5hHgg4Kqe5E1E1hSBpZS.png',
                'website'=>'https://www.gxswa.com/laravel/',
                'rootpath'=>'',
                'version'=>$config['version'],
                'releasedate'=>$config['release'],
                'addtime'=>TIMESTAMP,
                'dateline'=>TIMESTAMP
            ));

            //initializer default setting
            DB::table("core_settings")->insert([
                array(
                    'key'=>"swasocket",
                    'value'=>serialize(array(
                        'type'=>'remote',
                        'server'=>$installer['socket']['server'],
                        'api'=>$installer['socket']['webapi']
                    ))
                ),
                array(
                    'key'=>"page",
                    'value'=>serialize(array(
                        'title'=>'Whotalk即时通讯系统',
                        'icon'=>'/favicon.ico',
                        'logo'=>'/static/icon200.jpg',
                        'copyright'=>'© 2019-2022 Shenwa Studio. All Rights Reserved.',
                        'links'=>'<a class="copyright-link" href="https://www.whotalk.com.cn/" target="_blank">Whotalk官网</a><a class="copyright-link" href="https://chat.gxit.org/app/index.php?i=4&c=entry&m=swa_supersale&do=app&r=whotalkcloud.post" target="_blank">制作APP</a><a class="copyright-link" href="https://shimo.im/docs/XRkgJOKZ41UrFbqM" target="_blank">使用教程</a><a class="copyright-link" href="https://www.yuque.com/docs/share/84abf7ef-7d11-44f1-a510-ed70ef14ef3d?#" target="_blank">更新日志</a>',
                        'keywords'=>'Whotalk,IM,即时通讯,客服系统,SaaS即时通讯,开源,社交软件,WEB即时通讯,公众号客服,多开聊天软件,在线聊天,群聊,微擎模块,快速生成APP,聊天APP,群聊APP',
                        'description'=>'Whotalk是一款精巧灵活的SaaS-IM基础服务软件，提供即时通讯软件的基础功能，具备强大的开放能力和极高的自由度，可快速编译为各类平台应用软件。'
                    ))
                )
            ]);

            //servers update
            Artisan::call('server:update');

        }else{
            if (empty($authkey)) return $this->message('微擎站点安全码不能为空');
            $installer['database']['prefix'] = 'ims_';
            $uid = (int)$dbconnect->table('users')->where('founder_groupid',1)->orderBy('uid','asc')->value('uid');
            //数据表检测，待完善
        }
        //创建文件符号链接
        try {
            Artisan::call('storage:link');
        }catch (\Exception $exception){
            //创建文件映射失败
            Log::error('storage_link_fail',array('errno'=>-1,'message'=>$exception->getMessage()));
        }
        //写入配置文件
        $appname = !empty($manager['appname']) ? trim($manager['appname']) : 'Whotalk';
        $envfile_tmp = base_path(".env.example");
        $reader = fopen($envfile_tmp,'r');
        $envdata = fread($reader,filesize($envfile_tmp));
        fclose($reader);
        $baseurl = str_replace('/installer/render','',url()->current());
        $database = $installer['database'];
        $envdata = str_replace(array('{APP_NAME}','{AUTHKEY}','{BASEURL}','{FOUNDER}','{DB_HOST}','{DB_PORT}','{DB_DATABASE}','{DB_USERNAME}','{DB_PASSWORD}','{DB_PREFIX}'),array(
            $appname,$authkey,$baseurl,$uid,$database['host'],$database['port'],$database['database'],$database['username'],$database['password'],$database['prefix']
        ),$envdata);
        $envfile = base_path(".env");
        if (file_exists($envfile)){
            @unlink($envfile);
        }
        $writer = fopen($envfile,'w');
        if(!fwrite($writer,$envdata)){
            fclose($writer);
            return $this->message('文件写入失败，请检查根目录权限');
        }
        fclose($writer);
        //写入安装文件
        $instlock = base_path('storage/installed.bin');
        $writer = fopen($instlock,'w');
        $installer['baseurl'] = $baseurl;
        $complete = fwrite($writer,base64_encode(json_encode($installer)));
        fclose($writer);
        if(!$complete){
            return $this->message('文件写入失败，请检查storage目录权限');
        }
        Cache::forget('installer');
        return $this->message('恭喜您，安装成功！','','success');
    }

    public function agreement(){
        $isagree = (int)\request('isagree');
        $this->installer['isagree'] = $isagree;
        if (!Cache::put('installer',$this->installer,7200)){
            return $this->message();
        }
        return $this->message('操作成功','','success');
    }

    public function database(){
        if (!$this->installer['isagree']){
            return redirect()->action('installController@index');
        }
        return view('install.database',$this->installer);
    }

    public function dbDetect(Request $request){
        if ($request->isMethod('post')) {
            $dbconfig = $request->input('dbconfig');
            if (empty($dbconfig)) return $this->message();
            $dbconnect = intval($dbconfig['dbconnect']);
            $authkey = trim($dbconfig['authkey']);
            $founderpwd = trim($dbconfig['founderpwd']);
            $database = array(
                'driver'=>'mysql',
                'host'=>trim($dbconfig['db[host']),
                'port'=>intval($dbconfig['db[port']),
                'database'=>trim($dbconfig['db[database']),
                'username'=>trim($dbconfig['db[username']),
                'password'=>trim($dbconfig['db[password']),
                'prefix'=>trim($dbconfig['db[prefix'])
            );
            if ($dbconnect==1){
                if (empty($founderpwd)){
                    return $this->message('创始人登录密码不能为空');
                }
                if (empty($authkey)){
                    return $this->message('微擎站点安全码不能为空');
                }
                $database['prefix'] = 'ims_';
            }
            $isconnect = $this->dbConnect($database);
            if ($isconnect===false){
                return $this->message('数据库连接失败，请检查配置信息是否正确');
            }
            if ($dbconnect==1){
                try {
                    $founder = $isconnect->table('users')->select('uid','password','salt')->where('founder_groupid',1)->orderBy('uid','asc')->first();
                }catch (\Exception $e){
                    if (!empty($e->errorInfo) && $e->errorInfo[0]=='42S02'){
                        //Table dosn't exist
                        return $this->message('非微擎站点数据库');
                    }
                    return $this->message('数据库连接异常');
                }
                if (isset($founder->uid)){
                    $pwdhash = sha1("{$founderpwd}-{$founder->salt}-{$authkey}");
                    if ($pwdhash!=$founder->password){
                        return $this->message('创始人密码或安全码不正确');
                    }
                }else{
                    return $this->message('该创始人不存在');
                }
            }else{
                try {
                    $accounts = $isconnect->table('account')->count();
                }catch (\Exception $e){
                    //Todo something
                    unset($accounts);
                }
                if (isset($accounts)){
                    //Table exist
                    return $this->message('该数据库已经存在对应数据表');
                }
            }
            $this->installer['dbconnect'] = $dbconnect;
            $this->installer['database'] = $database;
            $this->installer['authkey'] = $authkey;
            Cache::forget('installer');
            if (!Cache::put('installer',$this->installer,7200)){
                return $this->message();
            }
            return $this->message('操作成功','','success');
        }
        return $this->message();
    }

    public function render(){
        if (!$this->installer['isagree']){
            return redirect()->action('installController@index');
        }
        if (isset($this->installer['database']['unix_socket'])){
            return redirect()->action('installController@database');
        }
        return view('install.render',$this->installer);
    }

    public function socket(){
        if (!$this->installer['isagree']){
            return redirect()->action('installController@index');
        }
        if (isset($this->installer['database']['unix_socket'])){
            return redirect()->action('installController@database');
        }
        $data = $this->installer;
        $data['usersign'] = sha1("{$data['database']['prefix']}-{$data['database']['username']}-{$data['authkey']}");
        return view('install.socket',$data);
    }

    public function wsDetect(Request $request){
        if ($request->isMethod('post')) {
            $wsconfig = $request->input('wsconfig');
            $socket = array(
                'type'=>trim($wsconfig['wstype']),
                'server'=>trim($wsconfig['ws_server']),
                'webapi'=>trim($wsconfig['ws_webapi'])
            );
            $socket['type'] = !in_array($socket['type'],['remote','local']) ? 'remote' : $socket['type'];
            if (!$socket['server']) return $this->message('SOCKET域名不能为空');
            if (!$socket['webapi']) return $this->message('WEB推送接口不能为空');
            $this->installer['socket'] = $socket;
            Cache::forget('installer');
            if (!Cache::put('installer',$this->installer)){
                return $this->message();
            }
            return $this->message('操作成功！','','success');
        }
        return $this->message();
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
