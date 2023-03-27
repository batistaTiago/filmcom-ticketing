<?php

namespace App\Exceptions;

use DomainException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($e instanceof DomainException) {
            $trace = $e->getTrace();
            $httpStatus = ($e->getCode() !== 0) ? $e->getCode() : 400;

            $appTrace = array_filter($trace, fn ($item) => !strpos($item['file'], 'vendor'));

            return response()->json([
                'error' => $e->getMessage(),
                'app_trace' => config('app.debug') ? $appTrace : null,
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], $httpStatus);
        }

        return parent::render($request, $e);
    }

    protected function shouldReturnJson($request, Throwable $e): bool
    {
        return true;
    }
}
