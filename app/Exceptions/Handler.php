<?php

namespace App\Exceptions;

use App\Models\SystemLogs;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (!$this->shouldReport($exception)){
            return;
        }
        $module = get_class($exception);
        $errCode = $exception->getCode();
        if ($exception instanceof NotFoundHttpException){
            $errCode = 404;
        }
        if ($exception instanceof QueryException){
            SystemLogs::database(
                $module,
                $exception->getSql(),
                $exception->getBindings(),
                "MySQL查询异常({$errCode})",
                0,
                false,
                $exception->getMessage()
            );
        }else{
            SystemLogs::error("服务器错误({$errCode})", $module, $exception->getMessage(), $errCode, [
                'file' => $exception->getFile() . ":" . $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'path'   => url()->current(),
                'method'  => request()->method(),
                'className' => get_class($exception)
            ]);
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    public function shouldReport(Exception $exception)
    {
        if ($exception instanceof AuthenticationException){
            //不记录未登录异常捕捉
            return false;
        }
        if ($exception instanceof NotFoundHttpException){
            //不记录未找到路由异常捕捉（开发环境除外）
            return (bool)config('system.setting.development');
        }

        return true;
    }
}
