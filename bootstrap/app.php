<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/admin.php',
            __DIR__.'/../routes/front.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\AdminAuthenticate::class,
            'admin.permission' => \App\Http\Middleware\AdminPermissionMiddleware::class,
            'front.locale' => \App\Http\Middleware\FrontLocaleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('admin/*') || $request->routeIs('admin.*')) {
                return response()->view('admin.not_found', [], 404);
            }
            return response()->view('front.not_found', [], 404);
        });

        // Handle 403 errors for admin routes
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('admin/*') || $request->routeIs('admin.*')) {
                return response()->view('admin.permission_denied', [], 403);
            }
        });

        // Handle 403 HTTP exceptions (from abort(403))
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403 && ($request->is('admin/*') || $request->routeIs('admin.*'))) {
                return response()->view('admin.permission_denied', [], 403);
            }
        });
    })->create();
