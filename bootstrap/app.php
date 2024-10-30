<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Agrega aquí tus middlewares globales si los tienes
        $middleware->alias([
            'csrf' => App\Http\Middleware\CustomCsrfMiddleware::class,
        ]);

        // Registro de middlewares de ruta
        $middleware->alias([
            'role' => App\Http\Middleware\CheckRol::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
