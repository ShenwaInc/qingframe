<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Capsule\Manager as Capsule;

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
                if (is_numeric($isconnect) && $isconnect==-1){
                    //Table doesn't exist
                    $this->message('非微擎站点数据库');
                }
                $founder = $isconnect->table('users')->select('uid','password','salt')->where('founder_groupid',1)->orderBy('uid','asc')->first();
                if (isset($founder->uid)){
                    $pwdhash = sha1("{$founderpwd}-{$founder->salt}-{$authkey}");
                    if ($pwdhash!=$founder->password){
                        $this->message('创始人密码或安全码不正确:'.$pwdhash);
                    }
                }else{
                    $this->message('该创始人不存在');
                }
            }else{
                if (!is_numeric($isconnect) || $isconnect!=-1){
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
        $capsule = new Capsule();
        $capsule->addConnection($database,'mysqldetect');
        $capsule->bootEloquent();
        try {
            $conn = $capsule->getConnection('mysqldetect');
            $conn->table('account')->count();
            return $conn;
        } catch (\Exception $e){
            if (empty($e->errorInfo)) return false;
            //Table doesn't exist
            if ($e->errorInfo[0]=='42S02') return -1;
            return @json_decode(json_encode($e->errorInfo),true);
        }
    }

}
