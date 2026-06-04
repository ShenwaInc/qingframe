<?php

/**
 * 云打包参数配置。
 */

 return [
    'auto_pack' => (bool)env('CLI_AUTOPACK', false),
    'exe_path' => env('CLI_PATH', ""),
    "debug_mode" => (bool)env('APP_DEBUG', false),
    "delay_time" => (int)env('CLI_DELAY', 5000),
    "debug_js"  => (bool)env('CLI_JS_DEBUG', false),
    "local_url" => env('CLI_LOCAL_URL', ""),
    "wxapp_library_version" => env('CLI_LIBRARY_VERSION', "3.15.1"),
    "package_api" => env('CLI_PACKAGE_API')
 ];