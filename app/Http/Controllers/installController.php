<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class installController extends Controller
{

    public $installer = ['isagree'=>0,'database'=>array(),'dbconnect'=>0,'authkey'=>'','socket'=>[]];
    public $defaultsocket = array(
        'type'=>'remote',
        'server'=>'wss://socket.whotalk.com.cn/wss',
        'webapi'=>'https://socket.whotalk.com.cn/api/message/sendMessageToUser'
    );

    function __construct(){
        $reset = (int)\request('reset');
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
            $this->message('安装失败，请重试');
        }
        if (!$this->installer['isagree']){
            $this->message('请先同意安装协议',url('installer'));
        }
        if (isset($this->installer['database']['unix_socket'])){
            $this->message('数据库未配置',url('installer/database'));
        }
        //写入数据库
        $dbconnect = $this->dbConnect($this->installer['database']);
        if ($dbconnect===false){
            $this->message('数据库连接失败，请检查配置信息是否正确');
        }
        $installer = $this->installer;
        $authkey = $installer['authkey'];
        if ($installer['dbconnect']==0){
            $authkey = \Str::random(8);
            $manager = $request->input('render');
            if (!isset($manager['username']) || trim($manager['username'])==''){
                $this->message('请填写您的超管账号');
            }
            if (!isset($manager['password']) || trim($manager['password'])==''){
                $this->message('请设置超管的登录密码');
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
                $this->message('数据库安装失败');
            }

            //initialize modules
            try {
                //import database
                Module::Initializer();
            }catch (\Exception $exception){
                $this->message('初始化数据失败');
            }

            //initialize group
            DB::table('whotalk_group')->insert(array(
                'id'=>random_int(100000,999999)
            ));

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
            if(!$uid) $this->message('数据写入失败');
            $founder['uid'] = $uid;
            $_W['user'] = $founder;
            $_W['uid'] = $uid;
            DB::table('users_profile')->insert(array(
                'avatar'=>'/static/icon200.jpg',
                'edittime'=>TIMESTAMP,
                'uid'=>$uid,
                'createtime'=>TIMESTAMP,
                'nickname'=>$founder['username']
            ));
            //create account
            $post = array('name'=>'Whotalk','description'=>'做社交从未如此简单');
            $uniacid = DB::table('uni_account')->insertGetId(array(
                'groupid' => 0,
                'default_acid' => 0,
                'name' => $post['name'],
                'description' => $post['description'],
                'title_initial' => 'W',
                'createtime' => TIMESTAMP,
                'create_uid' => $uid,
            ));
            if (empty($uniacid)) $this->message('系统初始化失败');
            $account_data = array('name' => $post['name']);

            $acid = Account::account_create($uniacid,$account_data);
            DB::table('uni_account')->where('uniacid',$uniacid)->update(array('default_acid' => $acid,'logo'=>'/static/icon200.jpg'));
            Account::user_role_insert($uniacid,$uid);

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
            //initialize whotalk setting
            $setting = array(
                'basic'=>array(
                    'name'=>$account_data['name'],
                    'description'=>$post['description'],
                    'icon'=>'/static/icon200.jpg',
                    'logo'=>'/static/icon200.jpg',
                    'defaultimg'=>'/static/icon200.jpg',
                    'defaultlanguage'=>'zh'
                ),
                'socket'=>array(
                    'type'=>$installer['socket']['type'],
                    'server'=>$installer['socket']['server'],
                    'api'=>$installer['socket']['webapi']
                ),
                'theme'=>array(
                    'link'=>'#0081ff',
                    'color'=>'#36373C',
                    'active'=>'#04be02',
                    'chatbg'=>'#5FB878',
                    'navbg'=>'bg-gray',
                    'actcolor'=>'limegreen'
                ),
                'album'=>array('switch'=>1,'square'=>1,'scan'=>1,'search'=>1,'popupscan'=>1,'allowpost'=>1)
            );
            $pars = array('module' => 'xfy_whotalk', 'uniacid' => $uniacid, 'settings'=>serialize($setting), 'enabled'=>1);
            DB::table('uni_account_modules')->insert($pars);

        }else{
            if (empty($authkey)) $this->message('微擎站点安全码不能为空');
            $installer['database']['prefix'] = 'ims_';
        }
        //写入配置文件
        $envfile_tmp = base_path(".env.example");
        $reader = fopen($envfile_tmp,'r');
        $envdata = fread($reader,filesize($envfile_tmp));
        fclose($reader);
        $baseurl = str_replace('/installer/render','',url()->current());
        $database = $installer['database'];
        $envdata = str_replace(array('{AUTHKEY}','{BASEURL}','{DB_HOST}','{DB_PORT}','{DB_DATABASE}','{DB_USERNAME}','{DB_PASSWORD}','{DB_PREFIX}'),array(
            $authkey,$baseurl,$database['host'],$database['port'],$database['database'],$database['username'],$database['password'],$database['prefix']
        ),$envdata);
        $envfile = base_path(".env");
        if (file_exists($envfile)){
            @unlink($envfile);
        }
        $writer = fopen($envfile,'w');
        if(!fwrite($writer,$envdata)){
            fclose($writer);
            $this->message('文件写入失败，请检查根目录权限');
        }
        fclose($writer);
        //写入安装文件
        $instlock = base_path('storage/installed.bin');
        $writer = fopen($instlock,'w');
        $installer['baseurl'] = $baseurl;
        $complete = fwrite($writer,base64_encode(json_encode($installer)));
        fclose($writer);
        if(!$complete){
            $this->message('文件写入失败，请检查storage目录权限');
        }
        $this->message('恭喜您，安装成功！','','success');
    }

    public function agreement(){
        $isagree = (int)\request('isagree');
        $this->installer['isagree'] = $isagree;
        if (!Cache::put('installer',$this->installer,7200)){
            $this->message();
        }
        $this->message('操作成功','','success');
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
            if (empty($dbconfig)) $this->message();
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
                    $this->message('创始人登录密码不能为空');
                }
                if (empty($authkey)){
                    $this->message('微擎站点安全码不能为空');
                }
                $database['prefix'] = 'ims_';
            }
            $isconnect = $this->dbConnect($database);
            if ($isconnect===false){
                $this->message('数据库连接失败，请检查配置信息是否正确');
            }
            if ($dbconnect==1){
                try {
                    $founder = $isconnect->table('users')->select('uid','password','salt')->where('founder_groupid',1)->orderBy('uid','asc')->first();
                }catch (\Exception $e){
                    if (!empty($e->errorInfo) && $e->errorInfo[0]=='42S02'){
                        //Table dosn't exist
                        $this->message('非微擎站点数据库');
                    }
                    $this->message('数据库连接异常');
                }
                if (isset($founder->uid)){
                    $pwdhash = sha1("{$founderpwd}-{$founder->salt}-{$authkey}");
                    if ($pwdhash!=$founder->password){
                        $this->message('创始人密码或安全码不正确:'.$pwdhash);
                    }
                }else{
                    $this->message('该创始人不存在');
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
                    $this->message('该数据库已经存在对应数据表');
                }
            }
            $this->installer['dbconnect'] = $dbconnect;
            $this->installer['database'] = $database;
            $this->installer['authkey'] = $authkey;
            Cache::forget('installer');
            if (!Cache::put('installer',$this->installer,7200)){
                $this->message();
            }
            $this->message('操作成功','','success');
        }
        $this->message();
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
            if (!$socket['server']) $this->message('SOCKET域名不能为空');
            if (!$socket['webapi']) $this->message('WEB推送接口不能为空');
            $this->installer['socket'] = $socket;
            Cache::forget('installer');
            if (!Cache::put('installer',$this->installer)){
                $this->message();
            }
            $this->message('操作成功！','','success');
        }
        $this->message();
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
