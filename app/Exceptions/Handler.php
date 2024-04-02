<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Explorin\Tebot\Services\Tebot;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        $user_id = auth()->user() ? auth()->user()->username : ' ';

        $logError = 'Message : ' . $exception->getMessage() .
            ' | URL : ' . request()->url() .
            ' | Params : ' . request()->getContent() .
            ' | User ID : ' . $user_id .
            ' | IP : ' . request()->getClientIp();
        if (config('app.log_tebot') == 1) {
            if (config('app.log_tebot_local')) {
                Tebot::alert($logError)->channel('log_skrd_local');
            } else {
                Tebot::alert($logError)->channel('log_skrd');
            }
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
