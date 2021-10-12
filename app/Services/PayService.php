<?php
namespace App\Services;
use AlipayTradeQueryRequest;
use AopClient;
use App\Utils\Code;
use App\Utils\WeModule;
use App\Models\CorePaylog;
use Illuminate\Support\Facades\Log;
use AlipayTradeWapPayContentBuilder;
use AlipayTradeService;

class PayService {

    /**
     * 创建支付
     * $type 支付类型 必传
     * $uniacid 平台id
     * $ordeInfo 订单信息
    */
    public static function create(string $type,array $orderInfo)
    {
        global $_W;
        $setting = SettingService::uni_load('payment',$_W['uniacid']);
        if (empty($setting)) return error(-1,'支付配置异常');
        $config = $setting['payment'];
        $result = false;
        switch($type){
            case 'alipay':
                if(empty($config['alipay'])){
                    return error(-1,'未配置支付宝支付');
                }
                if($config['alipay']['pay_switch'] != 1){
                    return error(-1,'未开启支付宝支付');
                }
                $new_config = $config['alipay'];
                if (empty($new_config['sign_type'])){
                    $new_config['sign_type'] = 'MD5';
                }
                $new_config['app_id'] = $config['alipay']['appid'];
                $new_config['alipay_public_key'] = $config['alipay']['publickey'];
                $new_config['merchant_private_key'] = $config['alipay']['privatekey'];
                $result = self::CreateAliPay($new_config,$orderInfo);
                break;
            case 'wechat':
                if(empty($config['wechat'])){
                    throw new \Exception('没有设置微信支付信息！',Code::ERROR);
                }
                if($config['wechat']['pay_switch'] != 1){
                    throw new \Exception('没有开启微信支付！',Code::ERROR);
                }
                $new_config = [];
                $new_config['app_id'] = $config['wechat']['appid'];
                $new_config['mch_id'] = $config['wechat']['mchid'];
                $new_config['api_key'] = $config['wechat']['apikey'];
                $result = self::CreateWxPay($new_config,$orderInfo);
                break;
            default:
                break;
        }
        return $result;
    }

    /**
     * 创建支付
     * $config 支付配置
     * $orderInfo 订单信息
     */
    static function CreateAliPay(array $config,array $orderInfo){
        global $_W;
        if ($config['sign_type']=='MD5'){
            $ps = array(
                'uniontid' => $orderInfo['uniontid'],
                'fee' => $orderInfo['fee'],
                'title' => $orderInfo['tag'],
                'service'=>'',
                'uniacid'=>$orderInfo['uniacid']
            );
            $ret = PaymentService::alipay_build($ps,$config);
            session_exit('<title>支付宝支付</title><script type="text/javascript" src="/static/payment/alipay/ap.js?v='.$_W['config']['release'].'"></script><script type="text/javascript">_AP.pay("'.$ret['url'].'")</script>');
            return true;
        }
        $config['notify_url'] = $_W['siteroot'] . 'payment/alipay';
        $config['return_url'] = $_W['siteroot'] . 'payment/return/alipay';
        $config['charset'] = 'UTF-8';
        $config['sign_type'] = 'RSA2';
        $config['gatewayUrl'] = 'https://openapi.alipay.com/gateway.do';
        $timeout_express = '1m'; //超时时间

        CloudService::LoadCom('alipay');

        $out_trade_no = $orderInfo['uniontid']; //商户订单号，商户网站订单系统中唯一订单号，必填
        $subject = $orderInfo['tag']; //订单名称，必填
        $total_amount = $orderInfo['fee']; //付款金额，必填
        $body = $orderInfo['tag']; //商品描述，可空

        try {
            //整合数据
            $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);

            //支付
            $payResponse = new AlipayTradeService($config);
            return $payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        }catch (\Exception $exception){
            $err = error(-1, $exception->getMessage());
            Log::error('CreateAliPayFail',$err);
            return $err;
        }
    }

    static function CreateWxPay(array $config,array $orderInfo){
        global $_W;
        $money = $orderInfo['fee'];  //金额
        $app_id  = $config['app_id'];   //应用 APPID
        $mch_id = $config['mch_id'];      //微信支付商户号
        $api_key = $config['api_key'];    //微信商户 API 密钥
        $out_trade_no = $orderInfo['uniontid'];//平台内部订单号
        $nonce_str = random(25);   //随机字符串
        $body = $orderInfo['tag'];  //内容
        $total_fee = $money; //金额
        $spbill_create_ip = $_W['clientip']; //IP
        $notify_url = "{$_W['siteroot']}payment/alipay"; //回调地址
        $trade_type = 'MWEB';  //交易类型 具体看 API 里面有详细介绍
        $scene_info ='{"h5_info":{"type":"Wap","wap_url":"'.$_W['siteroot'].'","wap_name":"支付"}}';//场景信息 必要参数
        $signA ="appid=$app_id&attach=$out_trade_no&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
        $strSignTmp = $signA."&key=$api_key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面 XML  是否正确
        $sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
        $post_data = "<xml>
                        <appid>$app_id</appid>
                        <mch_id>$mch_id</mch_id>
                        <body>$body</body>
                        <out_trade_no>$out_trade_no</out_trade_no>
                        <total_fee>$total_fee</total_fee>
                        <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
                        <notify_url>$notify_url</notify_url>
                        <trade_type>$trade_type</trade_type>
                        <scene_info>$scene_info</scene_info>
                        <attach>$out_trade_no</attach>
                        <nonce_str>$nonce_str</nonce_str>
                        <sign>$sign</sign>
                        </xml>";//拼接成 XML 格式
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址
        $dataxml = self::postXmlCurl($post_data,$url); //后台 POST 微信传参地址  同时取得微信返回的参数
        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的 XML 转换成数组
        //var_dump($objectxml);exit;
        if(strtolower($objectxml['result_code']) == 'success'){
            //同步回调跳转
            $redirecturl = $_W['siteroot'].'payment/return/wechat?out_trade_no='.$orderInfo['uniontid'].'&total_amount='.$money;
            $objectxml['mweb_url'] .=  '&redirect_url='.urlencode($redirecturl);
            header('Location:'.$objectxml['mweb_url']);
        }else{
            exit(json_encode($objectxml));
        }
        return true;
    }

    static function postXmlCurl($xml,$url,$second = 30){
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置 header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post 提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行 curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }else{
            $error = curl_errno($ch);
            curl_close($ch);
            exit("curl 出错，错误码:$error"."<br>");
        }
    }

    /**
     * 支付回调
     * $type 支付类型 string类型
     * $orderinfo 订单信息  array类型 用于校验支付信息是否正确
     */
    public static function notify(string $type,array $params,$from='notify'){
        $uniontid = trim($params['out_trade_no']);
        if (empty($uniontid)){
            return error(-1,'交易单号异常');
        }
        $paylog = CorePaylog::detail(0,$uniontid);
        if (empty($paylog)){
            return error(-1,'交易不存在');
        }
        $setting = SettingService::uni_load('payment',$paylog['uniacid']);
        $payresult = self::verify($params, $type, $setting['payment']);
        if (is_error($payresult)){
            return $payresult;
        }

        //修改订单
        $data['type'] = $type;
        $data['status'] = 1;
        if(CorePaylog::modify($paylog['plid'],$data)){
            if(!empty($info['module'])){
                global $_W;
                if ($_W['uniacid']!=$info['uniacid']){
                    $_W['uniacid'] = $info['uniacid'];
                    $_W['account'] = uni_fetch($_W['uniacid']);
                    $_W['openid'] = $info['openid'];
                }
                if ($from=='return' && $_W['member']['openid']!=$_W['openid']){
                    $_W['member'] = array('uid'=>0);
                    MemberService::AuthFetch($_W['openid']);
                }
                $ret = array();
                $ret['weid'] = $info['weid'];
                $ret['uniacid'] = $info['uniacid'];
                $ret['result'] = 'success';
                $ret['type'] = $info['type'];
                $ret['from'] = $from;
                $ret['tid'] = $info['tid'];
                $ret['uniontid'] = $info['uniontid'];
                $ret['transaction_id'] = $info['transaction_id'];
                $ret['user'] = $info['openid'];
                $ret['fee'] = $info['fee'];
                $ret['is_usecard'] = $info['is_usecard'];
                $ret['card_type'] = $info['card_type'];
                $ret['card_fee'] = $info['card_fee'];
                $ret['card_id'] = $info['card_id'];
                define('IN_API', true);
                if($info['module']=='core'){
                    return self::payResult($ret);
                }
                $WeModule = new WeModule();
                try {
                    $site = $WeModule->create($info['module']);
                    $site->payResult($ret);
                }catch (\Exception $exception){
                    Log::error('PaymentNotifyResult',error(-1,$exception->getMessage()));
                }
            }
            return true;
        }else{
            return error(-1,'订单更新失败('.Code::ERROR.')');
        }
    }

    public static function verify($params, $paytype, $config){
        $payresult = array('trade_status'=>'TRADE_SUCCESS','total_amount'=>0);
        if ($paytype=='alipay'){
            $payresult['out_trade_no'] = $params['out_trade_no'];
            $alipay = $config['alipay'];
            if($params['sign_type']=='RSA2'){
                $payresult['total_amount'] = $params['total_amount'];
                CloudService::LoadCom('aop');
                $aop = new AopClient;
                $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
                $aop->appId = $alipay['appid'];
                $aop->rsaPrivateKey = $alipay['privatekey'];
                $aop->format = "json";
                $aop->charset = "UTF-8";
                $aop->signType = "RSA2";
                $aop->alipayrsaPublicKey = $alipay['publickey'];
                //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
                $request = new AlipayTradeQueryRequest();

                $request->setBizContent("{" .
                    "\"out_trade_no\":\"".$params['out_trade_no']."\"," .
                    "\"trade_no\":\"\"," .
                    "\"org_pid\":\"\"," .
                    "      \"query_options\":[" .
                    "        \"trade_settle_info\"" .
                    "      ]" .
                    "  }");
                $response = $aop->execute($request);

                $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
                $resultCode = $response->$responseNode->code;
                if(!empty($resultCode)){
                    if ($resultCode==10000){
                        if ($response->$responseNode->trade_status=='TRADE_SUCCESS'){
                            return $payresult;
                        }else{
                            return error(-1,"支付未完成(".$response->$responseNode->trade_status.")");
                        }
                    }else{
                        $message = $response->$responseNode->sub_msg;
                        $message .= "(".$response->$responseNode->sub_code.")";
                        return error(-1, $message);
                    }
                } else {
                    return error(-1, '订单状态异常');
                }
            }else{
                foreach($params as $key => $value) {
                    if($key != 'sign' && $key != 'sign_type') {
                        $prepares[] = "{$key}={$value}";
                    }
                }
                sort($prepares);
                $string = implode('&', $prepares);
                $string .= $alipay['secret'];
                $sign = md5($string);
                if ($sign!=$params['sign']){
                    return error(-1, '签名验证失败');
                }
                if (isset($params['total_fee'])){
                    $payresult['total_amount'] = $params['total_fee'];
                }
                return $payresult;
            }
        }elseif($paytype=='wechat'){
            $input = file_get_contents('php://input');
            if (!empty($input) && empty($params['out_trade_no'])){
                $obj = isimplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
                $data = json_decode(json_encode($obj), true);
                if (empty($data)) {
                    $result = array(
                        'return_code' => 'FAIL',
                        'return_msg' => ''
                    );
                    echo array2xml($result);
                    exit;
                }
                if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
                    $result = array(
                        'return_code' => 'FAIL',
                        'return_msg' => empty($data['return_msg']) ? $data['err_code_des'] : $data['return_msg']
                    );
                    echo array2xml($result);
                    exit;
                }
                $params = $data;
            }
            $wechat = $config['wechat'];
            ksort($params);
            $string1 = '';
            foreach($params as $k => $v) {
                if($v != '' && $k != 'sign') {
                    $string1 .= "{$k}={$v}&";
                }
            }
            $wechat['signkey'] = ($wechat['version'] == 1) ? $wechat['key'] : (!empty($wechat['apikey']) ? $wechat['apikey'] : $wechat['signkey']);
            $sign = strtoupper(md5($string1 . "key={$wechat['signkey']}"));
            if ($sign != $params['sign']){
                return error(-1,'签名验证失败');
            }
            $payresult['out_trade_no'] = $params['out_trade_no'];
            $payresult['total_amount'] = $params['total_fee'] / 100;
            return $payresult;
        }
        return error(-1,'未知的支付方式');
    }

    public static function payResult($params){
        return true;
    }

}
?>
