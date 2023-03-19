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
        if ($e instanceof DomainException) {
            return response()->json([
                'error' => config('app.debug') ? $e->getMessage () : 'Server Error',
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], ($e->getCode() ?? 400));
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'error' => 'Unauthenticated'
            ], 401);
        }

        parent::render($request, $e);
    }

    protected function shouldReturnJson($request, Throwable $e): bool
    {
        return true;
    }
}
