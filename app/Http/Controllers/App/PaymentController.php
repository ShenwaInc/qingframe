<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    //支付回调：异步
    public function notify(Request $request, $payment){
        Log::info("Payment_{$payment}_Notify",$request->all());
        return $this->message($payment,'','success');
    }

    //支付回调：同步
    public function response(Request $request, $payment){

    }

}
