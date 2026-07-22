<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->throttleApi('60,1');

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'admin' => \App\Http\Middleware\Admin::class,
            'user' => \App\Http\Middleware\User::class,
            'casemanager' => \App\Http\Middleware\CaseManager::class,
            'supervisor' => \App\Http\Middleware\Supervisor::class,
            'manager' => \App\Http\Middleware\Manager::class,
            'financer' => \App\Http\Middleware\Financer::class,
            'api.auth.check' => \App\Http\Middleware\ApiAuthCheck::class,
            'api.auth' => \App\Http\Middleware\ApiAuth::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'api.response.auth' => \App\Http\Middleware\ApiResponseAuth::class,
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('database:backup')->daily()->timezone('Asia/Kolkata');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
