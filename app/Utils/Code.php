<?php
namespace App\Utils;

class Code
{ 
    /**
     * 状态码类型：
     *      success
     *      error
     *      warning
     */
    // 通用
    const SUCCESS = 0;
    const ERROR = 1;
    const PARAM_ERROR = -1001; // 参数错误
    const SERVER_INTERNAL_ERROR = -1004; // 服务器内部错误

    const CODEINFO = [
        Code::SUCCESS               => 'success',
        Code::ERROR                 => 'error',
        Code::PARAM_ERROR           => '参数错误',
        Code::SERVER_INTERNAL_ERROR => '服务器内部错误',
    ];

    static function msg($code)
    {
        return self::CODEINFO[$code];
    }

}

?>