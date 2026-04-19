<?php

use App\Auth\Infrastructure\Http\Middleware\AuthenticateAccessToken;
use App\Shared\Infrastructure\Http\Exception\ApiExceptionMapper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.token' => AuthenticateAccessToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $exception) {
            return app(ApiExceptionMapper::class)->toResponse($exception);
        });
    })->create();
