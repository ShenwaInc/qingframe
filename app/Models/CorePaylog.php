<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Utils\Code;

class CorePaylog extends Model
{
    protected $primaryKey = 'plid';
    protected $table = 'core_paylog';

    /**
     * 生成订单信息
     * @param $orderInfo array 订单信息 array类型
     * @param $identify string 插件标识 string类型
     * @return array|bool
     */
    public static function create(array $orderInfo, $identify='core')
    {
        global $_W;
        //允许插入的字段
        $allowFields = ['openid','tid','fee','module','uniontid','tag','is_usecard','card_type','card_id','card_fee','encrypt_code','is_wish','coupon'];
        $data = array_filter($orderInfo, function ($key) use ($allowFields) {
            return in_array($key, $allowFields);
        }, ARRAY_FILTER_USE_KEY);
        if (!isset($data['module'])){
            $data['module'] = $identify;
        }
        $data['uniacid'] = $_W['uniacid'];
        $data['acid'] = $_W['account']['acid'];
        $data['status'] = 0;  //生成订单时是未支付
        if (empty($data['uniontid'])){
            //$data['uniontid'] = Random::orderNumber();  //生成订单号
            $moduleId = DB::table('modules')->where(array('name' => $data['module']))->value('mid');
            $moduleId = empty($moduleId) ? '000000' : sprintf("%06d", $moduleId);
            $data['uniontid'] = date('YmdHis').$moduleId.random(8,true);
        }
        $data['plid'] = DB::table('core_paylog')->insertGetId($data);
        return $data['plid'] ? $data : false;
    }

     /**
      * 根据订单号/订单唯一标识id获取订单信息
      * @param $id int 订单id
      * @param $uniontid string 外部订单号
      * @return array
     */
    public static function detail(int $id,string $uniontid='')
    {
        if(empty($uniontid)){
            $where['plid'] = $id;
        }else{
            $where['uniontid'] = $uniontid;
        }
        return (array)DB::table('core_paylog')->where($where)->first();
    }

    /**
     * 更新订单信息
     * @param $id int 订单唯一标识ID int类型
     * @param $orderInfo array 订单信息
     * @return bool
     */
    public static function modify(int $id, array $orderInfo)
    {
        //允许修改的字段
        $allowFields = ['type','status'];
        $data = [];
        foreach($allowFields as $field){
            if(isset($orderInfo[$field])){
                $data[$field] = $orderInfo[$field];   //只修改传入的字段信息
            }
        }
        return DB::table('core_paylog')->where(['plid'=>$id])->update($data);
    }
}
?>
