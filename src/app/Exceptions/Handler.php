<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {

        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        if (env('APP_ENV') !== 'local') {
            \Log::error('エラー発生:', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

        if ($exception instanceof ValidationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'バリデーションエラー',
                    'errors' => $exception->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($exception->errors())->withInput();
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => '未認証のリクエストです',
                ], 401);
            }
            return redirect()->route('login')->withErrors(['login' => 'ログインが必要です']);
        }

        return parent::render($request, $exception);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
