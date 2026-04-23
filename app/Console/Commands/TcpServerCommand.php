<?php

namespace App\Console\Commands;

use App\Models\SystemLogs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use React\EventLoop\Loop;  // 替代原 Factory
use React\Socket\TcpServer;  // 替代原 Server
use GuzzleHttp\Client;
use React\Socket\ConnectionInterface;

class TcpServerCommand extends Command
{
    protected $signature = 'tcp:server 
                            {--host=0.0.0.0 : 监听地址} 
                            {--port=8080 : 监听端口} 
                            {--url= : 转发目标URL}';

    protected $description = '启动TCP服务器并转发数据到指定URL';

    protected $httpClient;

    public function __construct()
    {
        if(!class_exists('React\EventLoop\Loop')){
            include_once public_path('addons/community/vendor/autoload.php');
        }
        parent::__construct();
        $this->httpClient = new Client();
    }

    public function isBinary($data) {
        // 匹配 ASCII 0-31（控制字符）或 127（DEL）
        return preg_match('/[\x00-\x1F\x7F]/', $data) > 0;
    }

    public function handle()
    {
        $host = $this->option('host');
        $port = $this->option('port');
        $targetUrl = $this->option('url');

        if (!$host || !$port) {
            $this->error('请指定监听地址和端口');
            return 1;
        }
        if (!$targetUrl) {
            $this->error('请指定转发目标URL');
            return 1;
        }

        $this->info("正在启动TCP服务器: tcp://{$host}:{$port}");
        $this->info("数据将转发到: {$targetUrl}");

        // 获取默认事件循环（替代 Factory::create()）
        $loop = Loop::get();

        // 创建TCP服务器（使用 TcpServer 替代 Server）
        $server = new TcpServer("{$host}:{$port}", $loop);

        // 处理新连接
        $server->on('connection', function (ConnectionInterface $connection) use ($targetUrl) {
            $remoteAddress = $connection->getRemoteAddress();
            $this->info("新连接: {$remoteAddress}");

            // 处理收到的数据
            $connection->on('data', function ($data) use ($connection, $remoteAddress, $targetUrl) {
                $isBinary = $this->isBinary($data);
                $this->info( "[" . date('Y-m-d H:i:s') . "] 从 {$remoteAddress} 收到数据: " . ($isBinary ? '[二进制]' : json_encode($data)));

                /*Log::info("接收TCP数据", [
                    'data' => $isBinary ? base64_encode($data) : $data,
                    'remote_address' => $remoteAddress,
                    'timestamp' => date('Y-m-d H:i:s')
                ]);*/

                // 转发数据到指定URL
                try {
                    if($isBinary){
                        $response = $this->httpClient->post($targetUrl, [
                            'body' => $data, // 直接传递二进制数据（保持原样）
                            'headers' => [
                                'Content-Type' => 'application/octet-stream', // 二进制流类型
                            ],
                        ]);
                    }else{
                        $response = $this->httpClient->post($targetUrl, [
                            'form_params' => [
                                'data' => $data,
                                'remote_address' => $remoteAddress,
                                'timestamp' => time()
                            ]
                        ]);
                    }

                    $responseBody = $response->getBody()->getContents();
                    $this->info("数据转发成功，响应: {$responseBody}");
                    $connection->write("已收到并转发数据\n");
                } catch (\Exception $e) {
                    SystemLogs::systemRunning(
                        "TCP服务器数据转发异常",
                        'console:TcpServerCommand',
                        "TCP服务器转发数据到目标URL时发生异常：{$e->getMessage()}",
                        false,
                        [
                            'exception_message' => $e->getMessage(),
                            'exception_file' => $e->getFile(),
                            'exception_line' => $e->getLine(),
                            'exception_code' => $e->getCode(),
                            'exception_trace' => $e->getTrace(),
                            'target_url' => $targetUrl,
                            'remote_address' => $remoteAddress,
                            'is_binary' => $isBinary,
                        ]
                    );
                    $this->error("转发失败: " . $e->getMessage());
                    $connection->write("转发失败: " . $e->getMessage() . "\n");
                }
            });

            $connection->on('close', function () use ($remoteAddress) {
                $this->info("连接关闭: {$remoteAddress}");
            });

            $connection->on('error', function (\Exception $e) use ($remoteAddress) {
                $this->error("连接错误 ({$remoteAddress}): " . $e->getMessage());
            });
        });

        // 服务器错误处理
        $server->on('error', function (\Exception $e) {
            $this->error("服务器错误: " . $e->getMessage());
        });

        // 启动事件循环
        $loop->run();

        return 0;
    }
}