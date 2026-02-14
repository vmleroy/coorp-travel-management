<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();

        $middleware->trustProxies(at: '*');

        // Apply CORS middleware FIRST to all requests
        $middleware->prepend(\App\Http\Middleware\CorsMiddleware::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
        ]);

        // Disable redirect to login for API routes
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });

        // Custom error responses for common exceptions
        $exceptions->render(function (Throwable $e, $request) {
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null;
            }

            // ModelNotFoundException - Record not found
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro não encontrado.',
                    'error' => 'not_found'
                ], 404);
            }

            // ValidationException - Already handled by Laravel
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação.',
                    'errors' => $e->errors()
                ], 422);
            }

            // AuthenticationException
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Autenticação necessária. Por favor, faça login para acessar este recurso.',
                    'error' => 'unauthenticated'
                ], 401);
            }

            // AuthorizationException
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Você não possui permissão para executar esta ação.',
                    'error' => 'forbidden'
                ], 403);
            }

            // Generic Exception with message
            if ($e instanceof \Exception && $e->getMessage()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error' => 'error'
                ], 400);
            }

            return null;
        });
    })->create();
