<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    //支付回调：异步
    public function notify(Request $request, $payment){
        $query = $request->all();
        $input = file_get_contents('php://input');
        if(!empty($input)){
            $data = json_decode($input, true);
            if (is_array($data)) $query = array_merge($query,$data);
        }
        Log::info("Payment_{$payment}_Notify",$query);
        return $this->message($payment,'','success');
    }

    //支付回调：同步
    public function response(Request $request, $payment){

    }

}
