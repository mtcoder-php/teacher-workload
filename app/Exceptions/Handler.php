<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);
        
        // ✅ Inertia request yoki web request bo'lsa
        if ($request->inertia() || !$request->expectsJson()) {
            $status = $response->status();
            
            // 403 Forbidden
            if ($status === 403 || $e instanceof AuthorizationException) {
                return Inertia::render('Errors/403', [
                    'message' => $e->getMessage() ?: 'Sizda bu sahifaga kirish huquqi yo\'q',
                ])
                ->toResponse($request)
                ->setStatusCode(403);
            }
            
            // 404 Not Found
            if ($status === 404 || $e instanceof NotFoundHttpException) {
                return Inertia::render('Errors/404', [
                    'message' => 'Sahifa topilmadi',
                ])
                ->toResponse($request)
                ->setStatusCode(404);
            }
            
            // 500 Server Error
            if ($status === 500) {
                return Inertia::render('Errors/500', [
                    'message' => config('app.debug') ? $e->getMessage() : 'Server xatosi yuz berdi',
                ])
                ->toResponse($request)
                ->setStatusCode(500);
            }
        }
        
        return $response;
    }
}