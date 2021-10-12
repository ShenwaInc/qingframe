<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\PayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    //支付回调：异步
    public function notify(Request $request, $payment){
        $params = $request->all();
        $input = file_get_contents('php://input');
        if(!empty($input)){
            $data = json_decode($input, true);
            if (is_array($data)) $params = array_merge($params,$data);
        }
        $orderinfo = [
            'out_trade_no'=>$params['out_trade_no'],
            'total_amount'=>$params['total_amount']
        ];
        $result = PayService::notify($payment,$orderinfo);
        Log::info('PaymentNotify'.ucfirst($payment),$params);
        if($result){
            return $this->message('支付成功','','success');
        }else{
            return $this->message('支付失败，请重试','','success');
        }
    }

    //支付回调：同步
    public function response(Request $request, $payment){
        $params = $request->all();
        $input = file_get_contents('php://input');
        if(!empty($input)){
            $data = json_decode($input, true);
            if (is_array($data)) $params = array_merge($params,$data);
        }
        $orderinfo = [
            'out_trade_no'=>$params['out_trade_no'],
            'total_amount'=>$params['total_amount']
        ];
        $result =  PayService::notify($payment,$orderinfo, 'return');
        if($result){
            return $this->message('支付成功','','success');
        }else{
            return $this->message('支付失败，请重试','','success');
        }
    }

}
