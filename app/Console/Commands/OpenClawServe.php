<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Socket\SocketServer;
use Server\openclaw\model\OpenClawChatModel;

/**
 * OpenClaw Serve Command
 *
 * 运行一个持久化进程，通过 exec 调用 openclaw agent 命令
 *
 * 通过框架 websocket 微服务向客户端（user_socket_id）推送 OpenClaw 的响应结果。
 *
 * 配置: config/openclaw.php 或 .env
 *
 * API:
 * POST /chat - 发送消息（JSON: message, 可选 context_id, user_socket_id, message_id）
 * GET /status
 * POST /reset
 * POST /new-session
 */
class OpenClawServe extends Command
{
    protected $signature = 'openclaw:serve
                            {--http-port= : HTTP 服务监听端口}
                            {--session= : 会话标识符}';

    protected $description = '运行 OpenClaw HTTP 代理服务，通过 exec 模式调用 openclaw agent 命令';

    /** @var int */
    protected $httpPort;

    /** @var string */
    protected $sessionId;

    /** @var LoopInterface|null */
    protected $loop = null;

    /** @var string|null */
    protected $contextId = null;

    protected $messageId = 0;

    /** @var float */
    protected $startTime;
    protected $requestTime = 0;

    /** @var string */
    protected $mode = 'exec';

    /** @var string */
    protected $socketUserId = '';

    protected $command_name = 'openclaw';

    /** @var object|null 框架 websocket 微服务（可选） */
    protected $socketService;

    /** @var bool WebSocket 服务是否可用 */
    protected $socketServiceAvailable = false;

    /** @var int 最大请求体大小 (10MB) */
    const MAX_BODY_SIZE = 10 * 1024 * 1024;

    public function handle()
    {
        $this->startTime = microtime(true);
        $this->httpPort = (int) $this->option('http-port') ?:
            config('openclaw.http_port', 3005);
        $this->sessionId = $this->option('session') ?: config('openclaw.default_session');
        $this->contextId = null;
        $this->command_name = config('openclaw.command_name', 'openclaw');

        if (empty($this->sessionId)){
            $lastChat = OpenClawChatModel::query()->orderBy('id', 'desc')->limit(1)->first();
            if (!empty($lastChat)){
                $this->sessionId = $lastChat->session_id;
                $this->contextId = $lastChat->context_id;
            }
        }

        $this->info('🚀 OpenClaw HTTP 代理服务启动');
        $this->info("   会话 ID：{$this->sessionId}");
        $this->info("   HTTP 服务端口：http://0.0.0.0:{$this->httpPort}");
        $this->info('   实现方式：exec 调用 openclaw agent 命令');
        $this->line('');

        serv('openclaw')->Composer();

        $this->loop = Loop::get();

        $this->startHttpServer();
        $this->loop->run();

        return 0;
    }

    /**
     * 可选加载框架内 websocket，用于向浏览器端推送
     *
     * @return object|null
     */
    protected function resolveFrameworkWebsocketService()
    {
        $socket = serv('websocket');
        if (is_error($socket)) {
            return null;
        }
        if (empty($socket->enabled) || !method_exists($socket, 'Send')) {
            return null;
        }
        return $socket;
    }

    protected function startHttpServer()
    {
        try {
            $socket = new SocketServer("0.0.0.0:{$this->httpPort}", [], $this->loop);
            $http = new HttpServer($this->loop, [$this, 'handleHttpRequest']);
            $http->listen($socket);

            $this->info("✅ HTTP 服务器已启动，监听端口 {$this->httpPort}");
            $this->info('✅ 服务已启动，按 Ctrl+C 停止...');
            $this->line('');

            if (extension_loaded('pcntl')) {
                $this->loop->addSignal(SIGINT, function () {
                    $this->info("\n🛑 收到关闭信号，正在停止服务...");
                    $this->loop->stop();
                });

                $this->loop->addSignal(SIGTERM, function () {
                    $this->info("\n🛑 收到终止信号，正在停止服务...");
                    $this->loop->stop();
                });
            }
        } catch (\Exception $e) {
            $this->error("❌ HTTP 服务器启动失败：{$e->getMessage()}");
            $this->loop->stop();
        }
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return Response|PromiseInterface
     */
    public function handleHttpRequest($request)
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $this->debug("🌐 HTTP 请求：{$method} {$path}");

        switch ($path) {
            case '/chat':
                if ($method === 'POST') {
                    return $this->handleChat($request);
                }
                return $this->methodNotAllowed(['POST']);

            case '/status':
                if ($method === 'GET') {
                    return $this->handleStatus();
                }
                return $this->methodNotAllowed(['GET']);

            case '/reset':
                if ($method === 'POST') {
                    return $this->handleReset();
                }
                return $this->methodNotAllowed(['POST']);

            case '/new-session':
                if ($method === 'POST') {
                    return $this->handleNewSession($request);
                }
                return $this->methodNotAllowed(['POST']);

            default:
                return $this->notFound();
        }
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return Response|PromiseInterface
     */
    protected function handleChat($request)
    {
        $body = (string) $request->getBody();
        if (strlen($body) > self::MAX_BODY_SIZE) {
            return $this->jsonResponse(['error' => '请求体过大，最大允许 10MB'], 413);
        }

        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['message'])) {
            return $this->jsonResponse(
                ['error' => '无效的请求，需要 JSON 格式：{"message": "你的消息"}'],
                400
            );
        }

        $message = $data['message'];
        $contextId = $data['context_id'] ?? $this->contextId;
        if (!empty($data['user_socket_id'])) {
            $this->socketUserId = trim($data['user_socket_id']);
        }
        if (!empty($data['message_id']) && is_numeric($data['message_id'])) {
            $this->messageId = (int) $data['message_id'];
        }
        if (!empty($data['request_time'])) {
            $this->requestTime = (int) $data['request_time'];
        }

        $this->info('📨 收到 chat 请求：');
        $this->info("   消息内容：{$message}");
        if ($contextId) {
            $this->info("   上下文 ID：{$contextId}");
        }
        $this->line('');

        $startTime = microtime(true);
        $sendResult = $this->sendToOpenClaw($message, $contextId);

        if ($this->isPromise($sendResult)) {
            return $sendResult->then(
                function ($responseText) use ($startTime, $contextId) {
                    if ($contextId) {
                        $this->contextId = $contextId;
                    }
                    return $this->jsonResponse([
                        'success' => true,
                        'message' => '消息处理成功',
                        'content' => $responseText, // 直接返回字符串
                        'metadata' => [
                            'session_id' => $this->sessionId,
                            'context_id' => $this->contextId,
                            'request_context_id' => $contextId,
                            'timestamp' => time(),
                            'response_time' => microtime(true) - $startTime,
                        ],
                        'session_info' => [
                            'current_session' => $this->sessionId,
                            'current_context' => $this->contextId,
                            'service_status' => 'running',
                        ],
                    ]);
                },
                function ($reason) use ($startTime, $contextId, $message) {
                    $errMsg = $reason instanceof \Exception
                        ? $reason->getMessage()
                        : (is_string($reason) ? $reason : '未知错误');

                    return $this->jsonResponse([
                        'success' => false,
                        'error' => $errMsg,
                        'metadata' => [
                            'session_id' => $this->sessionId,
                            'context_id' => $this->contextId,
                            'timestamp' => time(),
                            'response_time' => microtime(true) - $startTime,
                        ],
                        'session_info' => [
                            'current_session' => $this->sessionId,
                            'current_context' => $this->contextId,
                            'service_status' => 'running',
                        ],
                    ], 500);
                }
            );
        }

        // 同步返回（仅当回退模式）
        try {
            $responseText = $sendResult; // 应为字符串
            if ($contextId) {
                $this->contextId = $contextId;
            }

            return $this->jsonResponse([
                'success' => true,
                'message' => '消息处理成功',
                'content' => $responseText,
                'metadata' => [
                    'session_id' => $this->sessionId,
                    'context_id' => $this->contextId,
                    'request_context_id' => $contextId,
                    'timestamp' => time(),
                    'response_time' => microtime(true) - $startTime,
                ],
                'session_info' => [
                    'current_session' => $this->sessionId,
                    'current_context' => $this->contextId,
                    'service_status' => 'running',
                ],
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
                'metadata' => [
                    'session_id' => $this->sessionId,
                    'context_id' => $this->contextId,
                    'timestamp' => time(),
                    'response_time' => microtime(true) - $startTime,
                ],
                'session_info' => [
                    'current_session' => $this->sessionId,
                    'current_context' => $this->contextId,
                    'service_status' => 'running',
                ],
            ], 500);
        }
    }

    /**
     * @param string $message
     * @param string|null $contextId
     * @return string|PromiseInterface
     */
    protected function sendToOpenClaw($message, $contextId = null)
    {
        return $this->sendToOpenClawByExec($message, $contextId);
    }

    /**
     * 异步执行 openclaw agent 命令（支持 ReactPHP 子进程，自动回退同步）
     *
     * @param string $message
     * @param string|null $contextId
     * @return PromiseInterface|string
     */
    protected function sendToOpenClawByExec($message, $contextId = null)
    {
        $this->debug('📤 使用 exec 调用 openclaw agent 命令');

        $command = [
            $this->command_name?:'openclaw',
            'agent',
            '--message=' . escapeshellarg($message),
            '--agent=main'
        ];
        if ($contextId) {
            $command[] = '--session-id=' . escapeshellarg($contextId);
        }

        // 尝试使用异步 ReactPHP 子进程
        if (class_exists('React\ChildProcess\Process')) {
            return $this->executeAsync($command, $contextId);
        } else {
            $this->warn('⚠️ react/child-process 未安装，使用同步 Process（会阻塞事件循环）');
            return $this->executeSync($command, $contextId);
        }
    }

    /**
     * 异步执行（推荐）
     *
     * @param array $command
     * @param string|null $contextId
     * @return PromiseInterface
     */
    protected function executeAsync(array $command, $contextId = null)
    {
        $deferred = new Deferred();
        $processCommand = implode(' ', $command);
        $process = new \React\ChildProcess\Process($processCommand);
        $process->start($this->loop);

        $output = '';
        $errorOutput = '';
        $bufferPath = null;
        $lastPushTime = microtime(true);
        $bufferAccumulator = '';

        // 如果指定了 messageId，创建临时文件用于流式存储
        if ($this->messageId) {
            $outputDir = base_path("servers/openclaw/output");
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            // 使用 messageId + 随机后缀避免冲突
            $uniqueId = uniqid($this->messageId . '_', true);
            $bufferPath = $outputDir . '/' . $uniqueId . '.txt';
            $this->debug("流式输出文件: {$bufferPath}");
        }

        $process->stdout->on('data', function ($chunk) use (&$output, &$bufferAccumulator, &$lastPushTime, $bufferPath, $contextId) {
            $output .= $chunk;
            $bufferAccumulator .= $chunk;

            // 流式推送：每 0.1 秒或累积 4096 字节推送一次
            $now = microtime(true);
            if (strlen($bufferAccumulator) >= 4096 || ($now - $lastPushTime) >= 0.1) {
                if ($bufferPath) {
                    file_put_contents($bufferPath, $bufferAccumulator, FILE_APPEND | LOCK_EX);
                    $this->pushMessageToClient([
                        'contentId' => $contextId,
                        'message_id' => $this->messageId,
                        'bufferPath' => $bufferPath,
                    ]);
                } else {
                    $this->pushMessageToClient([
                        'contentId' => $contextId,
                        'message_id' => $this->messageId,
                        'bufferText' => $bufferAccumulator,
                    ]);
                }
                $bufferAccumulator = '';
                $lastPushTime = $now;
            }
        });

        $process->stderr->on('data', function ($chunk) use (&$errorOutput) {
            $errorOutput .= $chunk;
        });

        $process->on('exit', function ($code) use ($deferred, &$output, &$errorOutput, $bufferPath, $contextId, $command) {
            // 推送最后剩余的缓冲区
            if (!empty($bufferAccumulator)) {
                if ($bufferPath) {
                    file_put_contents($bufferPath, $bufferAccumulator, FILE_APPEND | LOCK_EX);
                    $this->pushMessageToClient([
                        'contentId' => $contextId,
                        'message_id' => $this->messageId,
                        'bufferPath' => $bufferPath,
                    ]);
                } else {
                    $this->pushMessageToClient([
                        'contentId' => $contextId,
                        'message_id' => $this->messageId,
                        'bufferText' => $bufferAccumulator,
                    ]);
                }
            }

            if ($code !== 0) {
                $errMsg = !empty($errorOutput) ? $errorOutput : "命令执行失败，退出码: {$code}";
                $this->error("命令失败: " . implode(' ', $command));
                $this->error($errMsg);

                // 更新数据库错误记录
                if ($this->messageId) {
                    $chat = OpenClawChatModel::find($this->messageId);
                    if ($chat) {
                        $requestTime = $this->requestTime ?: strtotime($chat->created_at);
                        $chat->reply_takes = time() - $requestTime;
                        $chat->reply_fail_reason = $errMsg;
                        $chat->save();
                    }
                }
                $deferred->reject(new \RuntimeException($errMsg));
                return;
            }

            // 成功：解析输出
            $responseText = $this->parseOpenClawOutput($output);
            $this->debug("✅ 命令执行成功，响应长度: " . strlen($responseText));

            // 更新数据库成功状态
            if ($this->messageId) {
                $chat = OpenClawChatModel::find($this->messageId);
                if ($chat) {
                    $requestTime = $this->requestTime ?: strtotime($chat->created_at);
                    $chat->reply_status = 1;
                    $chat->reply_takes = time() - $requestTime;
                    $chat->save();
                }
                // 最终推送一次完成标记
                $this->pushMessageToClient([
                    'contentId' => $contextId,
                    'message_id' => $this->messageId,
                    'bufferPath' => $bufferPath,
                ], 'openclaw_response');
            }

            $deferred->resolve($responseText);
        });

        // 设置超时（120 秒）
        $timeoutTimer = $this->loop->addTimer(120, function () use ($process, $deferred) {
            if ($process->isRunning()) {
                $process->terminate();
                $deferred->reject(new \RuntimeException('命令执行超时（120 秒）'));
            }
        });

        $deferred->promise()->always(function () use ($timeoutTimer) {
            $this->loop->cancelTimer($timeoutTimer);
        });

        return $deferred->promise();
    }

    /**
     * 同步执行（回退模式，会阻塞事件循环）
     *
     * @param array $command
     * @param string|null $contextId
     * @return string
     * @throws \RuntimeException
     */
    protected function executeSync(array $command, $contextId = null)
    {
        $process = new \Symfony\Component\Process\Process($command);
        $process->setTimeout(120); // 设置超时
        $bufferPath = null;

        if ($this->messageId) {
            $outputDir = base_path("servers/openclaw/output");
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }
            $bufferPath = $outputDir . '/' . uniqid($this->messageId . '_', true) . '.txt';
        }

        $process->run(function ($type, $buffer) use ($contextId, $bufferPath) {
            $responseText = $this->parseOpenClawOutput($buffer);
            $responseData = ["contentId" => $contextId, "message_id" => $this->messageId];
            if ($bufferPath) {
                file_put_contents($bufferPath, $responseText, FILE_APPEND | LOCK_EX);
                $responseData['bufferPath'] = $bufferPath;
            } else {
                $responseData['bufferText'] = $responseText;
            }
            $this->pushMessageToClient($responseData);
            $this->debug("[buffer] " . $responseText);
        });

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput() ?: $process->getOutput();
            if ($this->messageId) {
                $chat = OpenClawChatModel::find($this->messageId);
                if ($chat) {
                    $requestTime = $this->requestTime ?: strtotime($chat->created_at);
                    $chat->reply_takes = time() - $requestTime;
                    $chat->reply_fail_reason = $error;
                    $chat->save();
                }
            }
            throw new \RuntimeException("命令执行失败: " . $error);
        }

        $output = $process->getOutput();
        if ($this->messageId) {
            $chat = OpenClawChatModel::find($this->messageId);
            if ($chat) {
                $requestTime = $this->requestTime ?: strtotime($chat->created_at);
                $chat->reply_status = 1;
                $chat->reply_takes = time() - $requestTime;
                $chat->save();
            }
            $this->pushMessageToClient([
                "contentId" => $contextId,
                "message_id" => $this->messageId,
                "bufferPath" => $bufferPath,
            ], "openclaw_response");
        }

        $responseText = $this->parseOpenClawOutput($output);
        return $responseText;
    }

    protected function parseOpenClawOutput($output)
    {
        $output = trim($output);

        if (strpos($output, '{') === 0 || strpos($output, '[') === 0) {
            $data = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($data['content']) && is_array($data['content'])) {
                    if (isset($data['content']['text'])) {
                        return $data['content']['text'];
                    }
                    return json_encode($data['content'], JSON_UNESCAPED_UNICODE);
                }
                if (isset($data['text'])) {
                    return $data['text'];
                }
                if (isset($data['message'])) {
                    return $data['message'];
                }
                return json_encode($data, JSON_UNESCAPED_UNICODE);
            }
        }

        return $output;
    }

    protected function getStatus()
    {
        $mode = $this->mode;
        $connection = [
            'http_port' => $this->httpPort,
            'http_url' => "http://0.0.0.0:{$this->httpPort}",
            'mode' => $mode,
        ];

        $implementation = '使用 exec 调用 openclaw agent 命令（' . (class_exists('React\ChildProcess\Process') ? '异步模式' : '同步回退模式') . '）';

        return [
            'status' => 'running',
            'message' => 'OpenClaw Serve 服务运行正常',
            'session' => [
                'session_id' => $this->sessionId,
                'context_id' => $this->contextId,
                'service_status' => 'running',
            ],
            'connection' => $connection,
            'service' => [
                'uptime' => $this->getUptime(),
                'start_time' => $this->startTime,
                'current_time' => time(),
                'version' => '1.3.0',
            ],
            'metadata' => [
                'timestamp' => time(),
                'endpoints' => [
                    '/chat' => 'POST - 发送消息',
                    '/status' => 'GET - 服务状态',
                    '/reset' => 'POST - 重置会话',
                    '/new-session' => 'POST - 新建会话',
                ],
                'implementation' => $implementation,
            ],
        ];
    }

    protected function handleStatus()
    {
        return $this->jsonResponse($this->getStatus());
    }

    protected function handleReset()
    {
        $this->info('🔄 收到重置会话请求');

        $oldContextId = $this->contextId;
        $this->contextId = null;

        $this->info("   旧上下文 ID：{$oldContextId}");
        $this->info('   上下文已清空');
        $this->line('');

        return $this->jsonResponse([
            'success' => true,
            'message' => '会话已重置，上下文关联已清空',
            'old_context_id' => $oldContextId,
            'new_context_id' => null,
            'session_id' => $this->sessionId,
            'timestamp' => time(),
            'reset_details' => [
                'context_history_cleared' => true,
                'new_session_available' => true,
            ],
        ]);
    }

    protected function handleNewSession($request)
    {
        $this->info('🆕 收到新建会话请求');

        $body = (string) $request->getBody();
        $data = json_decode($body, true);

        $customSessionId = $data['session_id'] ?? null;
        // 限制 session_id 长度，防止滥用
        if ($customSessionId !== null && strlen($customSessionId) > 255) {
            return $this->jsonResponse(['error' => 'session_id 长度不能超过 255 字符'], 400);
        }

        $oldSessionId = $this->sessionId;
        $oldContextId = $this->contextId;

        if ($customSessionId) {
            $this->sessionId = $customSessionId;
            $this->info("   使用自定义会话 ID：{$customSessionId}");
        } else {
            $this->sessionId = 'openclaw-serve-' . md5(gethostname() . time() . rand(1000, 9999));
            $this->info("   生成新会话 ID：{$this->sessionId}");
        }

        $this->contextId = null;

        $this->info("   旧会话 ID：{$oldSessionId}");
        $this->info("   旧上下文 ID：{$oldContextId}");
        $this->info("   新会话 ID：{$this->sessionId}");
        $this->info('   所有状态已重置');
        $this->line('');

        return $this->jsonResponse([
            'success' => true,
            'message' => '新会话已创建，完全清空历史上下文',
            'old_session_id' => $oldSessionId,
            'old_context_id' => $oldContextId,
            'new_session_id' => $this->sessionId,
            'new_context_id' => null,
            'timestamp' => time(),
            'session_details' => [
                'completely_new_session' => true,
                'history_cleared' => true,
                'custom_session_id' => $customSessionId !== null,
                'reset_level' => 'full',
            ],
        ]);
    }

    protected function getUptime()
    {
        $uptime = microtime(true) - $this->startTime;

        if ($uptime < 60) {
            return round($uptime, 1) . ' 秒';
        }
        if ($uptime < 3600) {
            return round($uptime / 60, 1) . ' 分钟';
        }

        return round($uptime / 3600, 1) . ' 小时';
    }

    protected function jsonResponse(array $data, $statusCode = 200)
    {
        return new Response(
            $statusCode,
            ['Content-Type' => 'application/json; charset=utf-8'],
            json_encode($data, JSON_UNESCAPED_UNICODE)
        );
    }

    protected function notFound()
    {
        return $this->jsonResponse(['error' => 'Not Found'], 404);
    }

    protected function methodNotAllowed(array $allowedMethods)
    {
        return new Response(
            405,
            ['Allow' => implode(', ', $allowedMethods)],
            json_encode(['error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE)
        );
    }

    protected function debug($message)
    {
        if ($this->option('verbose')) {
            $this->line("   [DEBUG] {$message}");
        }
    }

    /**
     * @param mixed $value
     */
    protected function isPromise($value)
    {
        return is_object($value) && $value instanceof PromiseInterface;
    }

    protected function pushMessageToClient($data, $type = 'openclaw_buffer')
    {
        // 懒加载并检查 WebSocket 服务可用性
        if ($this->socketService === null) {
            $this->socketService = serv('websocket');
        }

        if (!$this->socketService->enabled) {
            $this->error($this->socketService->error);
            return;
        }
        $this->socketServiceAvailable = true;

        try {
            $this->debug('📤 通过 WebSocket 服务推送消息');

            $userId = $this->getWebSocketUserId();
            if (empty($userId)) {
                $this->debug('未指定用户 ID，跳过推送');
                return;
            }

            $this->debug("  目标用户ID: {$userId}");

            $pushMessage = [
                'type' => $type,
                'data' => $data,
                'timestamp' => time(),
            ];

            $res = $this->socketService->Send($pushMessage, $userId);

            if (is_error($res) || empty($res)) {
                $this->error('❌ WebSocket 推送失败: ' . ($res['message'] ?? '推送结果异常'));
            } else {
                $this->info("✅ 消息已通过 WebSocket 服务推送到客户端: {$userId}");
            }
        } catch (\Exception $e) {
            $this->error('❌ WebSocket 推送失败: ' . $e->getMessage());
            $this->socketServiceAvailable = false;
            $this->socketService = null;
        }
    }

    protected function getWebSocketUserId()
    {
        if ($this->socketUserId) {
            return $this->socketUserId;
        }
        if (!$this->socketService || !$this->socketServiceAvailable) {
            return '';
        }
        if (method_exists($this->socketService, 'getKey')) {
            return $this->socketService->getKey(1, 'openclaw');
        }
        return '';
    }
}
