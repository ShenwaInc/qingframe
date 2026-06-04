<?php

/**
 * OpenClaw HTTP 代理（openclaw:serve）配置。
 */
return [
    'http_port' => (int) env('OPENCLAW_HTTP_PORT', 3005),
    'http_host' => env('OPENCLAW_HTTP_HOST', '0.0.0.0'),
    'http_scheme' => env('OPENCLAW_SCHEME', 'http'),
    'http_server' => env('OPENCLAW_HTTP_SERVER', 'local'),
    'command_name' => env('OPENCLAW_COMMAND', 'openclaw'),
    'default_session' => env('OPENCLAW_SESSION', '')
];
