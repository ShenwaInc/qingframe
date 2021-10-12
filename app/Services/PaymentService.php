<?php


namespace App\Services;

!defined('ALIPAY_GATEWAY') && define('ALIPAY_GATEWAY', 'https://mapi.alipay.com/gateway.do');

class PaymentService
{

    static function alipay_build($params, $alipay = array(), $is_site_store = false) {
        global $_W;
        $tid = $params['uniontid'];
        $set = array();
        $set['service'] = 'alipay.wap.create.direct.pay.by.user';
        $set['partner'] = $alipay['partner'];
        $set['_input_charset'] = 'utf-8';
        $set['sign_type'] = 'MD5';
        $set['notify_url'] = $_W['siteroot'] . 'payment/alipay';
        $set['return_url'] = $_W['siteroot'] . 'payment/return/alipay';
        $set['out_trade_no'] = $tid;
        $set['subject'] = $params['title'];
        $set['total_fee'] = $params['fee'];
        $set['seller_id'] = $alipay['account'];
        $set['payment_type'] = 1;
        $set['body'] = $is_site_store ? 'site_store' : $params['uniacid'];
        if ($params['service'] == 'create_direct_pay_by_user') {
            $set['service'] = 'create_direct_pay_by_user';
            $set['seller_id'] = $alipay['partner'];
        } else {
            $set['app_pay'] = 'Y';
        }
        $prepares = array();
        foreach($set as $key => $value) {
            if($key != 'sign' && $key != 'sign_type') {
                $prepares[] = "{$key}={$value}";
            }
        }
        sort($prepares);
        $string = implode('&', $prepares);
        $string .= $alipay['secret'];
        $set['sign'] = md5($string);

        $response = HttpService::ihttp_request(ALIPAY_GATEWAY . '?' . http_build_query($set, '', '&'), array(), array('CURLOPT_FOLLOWLOCATION' => 0));
        if (empty($response['headers']['Location'])) {
            session_exit(iconv('gbk', 'utf-8', $response['content']));
        }
        return array('url' => $response['headers']['Location']);
    }

}
