<?php

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
            $redirect = $_W['siteroot'] . 'app/index.php?' . $urls['query'];
        }
        if($redirect == '') {
            $type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'info';
        } else {
            $type = in_array($type, array('success', 'error', 'info', 'warning', 'ajax', 'sql')) ? $type : 'success';
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
        }
        $label = $type;
        if($type == 'error') {
            $label = 'danger';
        }
        if($type == 'ajax' || $type == 'sql') {
            $label = 'warning';
        }
        if (defined('IN_API')) {
            session_exit($msg);
        }
        abort(410, $msg);
        exit();
    }
}
