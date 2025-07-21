<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Sales;
use App\Http\Middleware\Driver;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\User;

use App\Http\Middleware\SetAppLang;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' =>  Admin::class,
            'user' =>  User::class,
            'verified' => EnsureEmailIsVerified::class,

            'lang' =>  SetAppLang::class,

            'sales' =>  Sales::class,
            'driver' =>  Driver::class,

        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
