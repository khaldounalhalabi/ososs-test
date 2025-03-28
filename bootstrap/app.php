<?php

use App\Exceptions\Handler;
use App\RoleEnum;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['api'])
                ->prefix('api/')
                ->name('api.')
                ->group(base_path('routes/api/public.php'));

            Route::middleware(['api', 'auth:api', 'role:' . RoleEnum::CUSTOMER->value])
                ->prefix('/api/customer')
                ->name('api.customer.')
                ->group(base_path('routes/api/customer.php'));

            Route::middleware(['api', 'role:' . RoleEnum::ADMIN->value])
                ->prefix('/api/admin')
                ->name('api.admin.')
                ->group(base_path('routes/api/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if (request()->acceptsJson()) {
            $exceptions->render(function (Exception $exception, Request $request) {
                return Handler::create()->handleException($request, $exception);
            });
        }
    })->create();
