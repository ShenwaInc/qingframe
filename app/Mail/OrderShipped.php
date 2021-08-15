<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Shipped)
    {
        //
        $this->Shipped = $Shipped;
    }

    public $Shipped = null;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        global $_W;
        $config = $_W['setting']['mail'];
        if (!$this->Shipped['global'] && $_W['uniacid']>0){
            $row = pdo_get('uni_settings', array('uniacid' => $_W['uniacid']), array('notify'));
            $row['notify'] = @unserialize($row['notify']);
            if (!empty($row['notify']) && !empty($row['notify']['mail'])) {
                $config = $row['notify']['mail'];
            }
        }
        return $this->from($config['username'])->subject($this->Shipped['subject'])->view('mailto',array('body'=>$this->Shipped['body']));
    }
}
