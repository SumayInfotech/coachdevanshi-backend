<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        //GLOBAL MIDDLEWARE (runs on every web request)
        // $middleware->append([
        //     \App\Http\Middleware\SetAppLanguage::class,
        // ]);
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            // 'frontend.auth' => \App\Http\Middleware\FrontendAuth::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'set.language' => \App\Http\Middleware\SetAppLanguage::class,
        ]);
    })
    ->withProviders([
        App\Providers\AuthServiceProvider::class,
        App\Providers\DynamicMailConfigServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
       $exceptions->render(function (AuthenticationException $exception, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'data' => null
            ], 401);
        }
    });
    })->create();
