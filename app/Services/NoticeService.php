<?php

namespace App\Services;

use AlibabaCloud\SDK\DysmsapiClient;
use Illuminate\Support\Facades\DB;

class NoticeService
{

    /**
     * @param string|numeric $mobile 手机号/UID
     * @param string $templateid 短信模板编号
     * @param array $param 短信发送参数值
    */
    static function SendSms($mobile,$templateid,$param=array()){
        $regular = '/^1[3-9]\d{9}$/';
        if (!$templateid) return error(-1,'短信模板ID未配置');
        if (!preg_match($regular,$mobile)){
            $mobile = DB::table('mc_members')->where(array('uid'=>intval($mobile)))->value('mobile');
        }
        if (!preg_match($regular, $mobile)){
            return error(-1,'手机号不正确');
        }
        $setting = SettingService::uni_load('notice');
        if ($setting['notice']['sms']['type']=='aliyun'){
            //阿里云短信
            CloudService::LoadCom('aliyun');
            $cfg = $setting['notice']['sms']['aliyun'];
            try {
                //国内短信
                $dysmsapi = new DysmsapiClient($cfg['appid'], $cfg['secret']);
                $dysmsapi->main([
                    "phoneNumbers" => $mobile,
                    "signName" => $cfg['sign'],
                    "templateCode" => $templateid,
                    "templateParam" => json_encode($param)
                ]);
            } catch (\Exception $e) {
                if ($templateid==$cfg['templaid']){
                    //验证码模板ID
                    pdo_delete('uni_verifycode', array('receiver' => $mobile));
                }
                return error(-1, $e->getMessage());
            }
            return true;
        }
        return error(-1,'未配置短信发送');
    }

}
