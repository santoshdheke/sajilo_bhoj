<?php

use App\Http\Middleware\EnsureSubscriptionChosen;
use App\Http\Middleware\EnsureUserHasPermission;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth:web', EnsureSubscriptionChosen::class])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [ResolveTenant::class]);
        $middleware->alias([
            'permission' => EnsureUserHasPermission::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->is('admin/*')
                ? route('admin.auth.show_login_form')
                : route('frontend.auth.show_login_form');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
