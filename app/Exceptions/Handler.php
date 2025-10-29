<?php

namespace App\Exceptions;

use App\Models\SystemLog;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        if (!($exception instanceof NotFoundHttpException) && Schema::hasTable('system_logs')) {
            SystemLog::error('服务器错误(' . $exception->getCode() . ')', 'Exception:report', $exception->getMessage(), $exception->getCode(), [
                'file' => $exception->getFile() . ":" . $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'path'   => url()->current(),
                'method'  => request()->method(),
                'params'  => request()->all(),
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
}
