<?php

namespace App\Helpers;

use Illuminate\Support\Facades\View;

if (!function_exists('message')){
    function message($msg, $redirect = '', $type = 'error') {
        global $_W;
        if (empty($type) && $_W['account']->supportVersion) {
            $type = 'ajax';
        }
        if($redirect == 'refresh') {
            $redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
        } elseif (!empty($redirect) && !strexists($redirect, 'http://') && !strexists($redirect, 'https://')) {
            $urls = parse_url($redirect);
            $redirect = $_W['siteroot'] . 'app?' . $urls['query'];
        }
        if($_W['isajax'] || $type == 'ajax') {
            $vars = array();
            $vars['message'] = $msg;
            $vars['redirect'] = $redirect;
            $vars['type'] = $type;
            session_exit(json_encode($vars));
        }
        if (empty($msg) && !empty($redirect)) {
            header('location: '.$redirect);
            session_exit();
        }
        if (defined('IN_API')) {
            session_exit($msg);
        }
        View::share('_W',$_W);
        echo response()->view('mmessage',array('message'=>$msg,'redirect'=>$redirect,'type'=>$type))->content();
        session_exit();
    }
}
