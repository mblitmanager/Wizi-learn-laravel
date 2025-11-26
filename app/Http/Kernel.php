<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // ... you can add global middleware here if needed
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
    'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Application specific
        'jwt.auth' => \App\Http\Middleware\JwtMiddleware::class,
        'is.admin' => \App\Http\Middleware\IsAdmin::class,
        'is.formateur' => \App\Http\Middleware\IsFormateur::class,
        'is.commercial' => \App\Http\Middleware\IsCommercial::class,
        'isAdmin' => \App\Http\Middleware\IsAdmin::class,
        'isFormateur' => \App\Http\Middleware\IsFormateur::class,
        'isCommercial' => \App\Http\Middleware\IsCommercial::class,
        'track.user' => \App\Http\Middleware\TrackUserActivity::class,
        'detectClient' => \App\Http\Middleware\DetectClientPlatform::class,
    ];
}
