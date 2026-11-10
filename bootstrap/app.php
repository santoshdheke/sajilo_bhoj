<?php

use App\Http\Middleware\EnsureOwnerEmailVerified;
use App\Http\Middleware\EnsureWebsiteEnabled;
use App\Http\Middleware\EnsureSubscriptionActive;
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
            Route::middleware(['web', 'auth:web', EnsureOwnerEmailVerified::class, EnsureSubscriptionChosen::class, EnsureSubscriptionActive::class])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [ResolveTenant::class, EnsureWebsiteEnabled::class]);
        // The tenant must be known before route models are loaded ({menuCategory}, {order}, ...),
        // otherwise the branch/company scopes are not applied and any id from any restaurant resolves.
        $middleware->prependToPriorityList(
            before: \Illuminate\Routing\Middleware\SubstituteBindings::class,
            prepend: ResolveTenant::class,
        );
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
