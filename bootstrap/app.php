<?php

use App\Http\Middleware\RequireWorkspace;
use App\Support\Workspace;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Each module (Modules/Restaurant, Modules/Shop) registers its own routes, middleware and guest
// redirects, only for hosts whose company is of its type (App\Support\Workspace::serves()).
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [RequireWorkspace::class]);
        // Decide "no workspace here" before route models load or a module resolves its tenant.
        $middleware->prependToPriorityList(before: SubstituteBindings::class, prepend: RequireWorkspace::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Unknown URLs never reach route middleware. On a host without a workspace, send them to "/" too.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->path() === '/' || $request->expectsJson() || ! Workspace::missing($request->getHost())) {
                return null;
            }

            return redirect('/');
        });
    })->create();
