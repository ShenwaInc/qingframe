<?php
namespace App\Utils;

class Random
{
    /**
     * 生成订单号，按时间顺序
    */
    public static function orderNumber()
    {
        return date('YmdHis').rand(100000,999999);
    }
}
?>