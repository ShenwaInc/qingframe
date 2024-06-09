<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyCode extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code=null)
    {
        //
        if (!empty($code)){
            $this->code = $code;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailFrom = config('mail.from');
        return $this->from($mailFrom['address'])->subject("【{$mailFrom['name']}】平台验证码")->text('mailto', array('body'=>"您的验证码是：{$this->code}，15分钟内有效。祝您生活愉快！【{$mailFrom['name']}】"));
    }
}
