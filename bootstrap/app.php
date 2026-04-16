<?php

use App\Http\Middleware\ApplyPreferredLocale;
use App\Http\Middleware\ForceHttpsWhenConfigured;
use App\Http\Middleware\SetRobotsTag;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(
            prepend: [
                ForceHttpsWhenConfigured::class,
            ],
            append: [
                ApplyPreferredLocale::class,
            ],
        );

        $middleware->alias([
            'seo.noindex' => SetRobotsTag::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
