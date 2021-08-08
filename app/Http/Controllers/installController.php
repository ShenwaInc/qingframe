<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class installController extends Controller
{

    public $installer = ['isagree'=>0,'database'=>array(),'dbconnect'=>0,'authkey'=>''];

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

}
