<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HttpController extends Controller
{

    public function ServerApi($server, $segment1='index', $segment2=''){
        global $_W;
        $_W['isapi'] = true;
        $ctrl = trim($segment1);
        if (!empty($segment2)){
            $ctrl = implode("/", array($ctrl, trim($segment2)));
        }
        $data = serv($server)->HttpRequest('api', $ctrl);
        if (is_error($data)) return $this->message($data['message']);
        if (isset($data['message']) && isset($data['type'])){
            return $this->message($data["message"], $data['redirect'], $data['type']);
        }
        session_exit(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function ServerApp($server, $segment1='index', $segment2=''){
        $ctrl = trim($segment1);
        if (!empty($segment2)){
            $ctrl = implode("/", array($ctrl, trim($segment2)));
        }
        $data = serv($server)->HttpRequest('app', $ctrl);
        if (is_error($data)) return $this->message($data['message']);
        if (isset($data['message']) && isset($data['type'])){
            return $this->message($data["message"], $data['redirect'], $data['type']);
        }
        return $data;
    }

}
