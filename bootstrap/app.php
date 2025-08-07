<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin.guest' => \App\Http\Middleware\adminRedirect::class,
        'admin.auth' => \App\Http\Middleware\adminAuthenticate::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class, //  
        'setlocale' => \App\Http\Middleware\SetLocale::class,  
    ]);
 

        $middleware->group('web', [
         \Illuminate\Session\Middleware\StartSession::class,    
    \App\Http\Middleware\SetLocale::class,
    ]);
        
        $middleware->redirectTo(
            guests: '/account/login',
            users: '/account/dashboard',
        );

        })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
        
    })->create();    

