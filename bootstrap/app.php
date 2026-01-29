<?php


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\TrackUserActivity; // Ajoutez cette ligne

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin' => \App\Http\Middleware\IsAdmin::class,
            'trackActivity' => TrackUserActivity::class,
            'isFormateur' => \App\Http\Middleware\IsFormateur::class,
            'detectClient' => \App\Http\Middleware\DetectClientPlatform::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'checkSyncSecret' => \App\Http\Middleware\CheckSyncSecret::class,
        ]);

        // Ajoutez le middleware aux groupes web et api
        $middleware->appendToGroup('web', TrackUserActivity::class);
        $middleware->appendToGroup('api', TrackUserActivity::class);

        // Gardez votre middleware JWT existant
        $middleware->appendToGroup('api', JwtMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
