<?php

use App\Http\Middleware\CheckActiveUser;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\ShareNotifications;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inertia\Inertia;
use Illuminate\Auth\Access\AuthorizationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\WorkloadServiceProvider::class, // <-- Qo'shing
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
            'permission' => CheckPermission::class,
            'active' => CheckActiveUser::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            CheckActiveUser::class,
            ShareNotifications::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ✅ 403 va boshqa xatolarni handle qilish
        $exceptions->respond(function ($response, $exception, $request) {
            // Faqat Inertia requests uchun
            if (!$request->inertia()) {
                return $response;
            }

            $status = $response->status();

            // 403 - Forbidden
            if ($status === 403 || $exception instanceof AuthorizationException) {
                return Inertia::render('Errors/403', [
                    'message' => $exception->getMessage() ?: 'Sizda bu sahifaga kirish huquqi yo\'q',
                ])
                ->toResponse($request)
                ->setStatusCode(403);
            }

            // 404 - Not Found
            if ($status === 404) {
                return Inertia::render('Errors/404', [
                    'message' => 'Sahifa topilmadi',
                ])
                ->toResponse($request)
                ->setStatusCode(404);
            }

            // 500 - Server Error
            if ($status === 500) {
                return Inertia::render('Errors/500', [
                    'message' => config('app.debug') ? $exception->getMessage() : 'Server xatosi yuz berdi',
                ])
                ->toResponse($request)
                ->setStatusCode(500);
            }

            return $response;
        });
    })->create();
