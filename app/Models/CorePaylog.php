<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Utils\Random;
use App\Utils\Code;

class CorePaylog extends Model
{
    protected $primaryKey = 'plid';
    protected $table = 'core_paylog';

    /**
     * 生成订单信息
     * $orderinfo 订单信息 array类型
     * $identify 插件标识 string类型
     */
    public static function create(array $orderinfo,$identify='core')
    {
        global $_W;
        //允许插入的字段
        $allowFields = ['openid','tid','fee','module','uniontid','tag','is_usecard','card_type','card_id','card_fee','encrypt_code','is_wish','coupon'];
        $data = [];
        foreach ($orderinfo as $key=>$value){
            if (in_array($key, $allowFields)){
                $data[$key] = $value;
            }
        }
        if (!isset($data['module'])){
            $data['module'] = $identify;
        }
        $data['uniacid'] = $_W['uniacid'];
        $data['acid'] = $_W['account']['acid'];
        $data['status'] = 0;  //生成订单时是未支付
        if (empty($data['uniontid'])){
            //$data['uniontid'] = Random::orderNumber();  //生成订单号
            $moduleid = DB::table('modules')->where(array('name' => $data['module']))->value('mid');
            $moduleid = empty($moduleid) ? '000000' : sprintf("%06d", $moduleid);
            $data['uniontid'] = date('YmdHis').$moduleid.random(8,true);
        }
        try{
            $data['plid'] = DB::table('core_paylog')->insertGetId($data);
            return $data;
        }catch(\Throwable $e){
            throw new \Exception($e->getMessage(),Code::SERVER_INTERNAL_ERROR);
        }
    }

     /**
     * 根据订单号/订单唯一标识id获取订单信息
     * $plid 订单唯一标识id int类型
     * $ordernumber 订单号 string
     */
    public static function detail(int $plid,string $ordernumber='')
    {
        if($ordernumber == ''){  //订单号为空时按id查询
            $where['plid'] = $plid;
        }else{
            $where['uniontid'] = $ordernumber;
        }
        try{
            return DB::table('core_paylog')->where($where)->get()->toArray()[0];
        }catch(\Throwable $e){
            throw new \Exception($e->getMessage(),Code::SERVER_INTERNAL_ERROR);
        }
    }

    /**
     * 更新订单信息
     * $plid 订单唯一标识ID int类型
     * $orderinfo 订单信息
     */
    public static function modify(int $plid,array $orderinfo)
    {
        //允许修改的字段
        $allowFields = ['type','status'];
        $data = [];
        foreach($allowFields as $field){
            if(isset($orderinfo[$field])){
                $data[$field] = $orderinfo[$field];   //只修改传入的字段信息
            }
        }
        try{
            DB::table('core_paylog')->where(['plid'=>$plid])->update($data);
            return true;
        }catch(\Throwable $e){
            throw new \Exception($e->getMessage(),Code::SERVER_INTERNAL_ERROR);
        }
    }
}
?>
