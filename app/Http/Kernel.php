<?php namespace DreamFactory\Enterprise\Installer\Http;

use DreamFactory\Enterprise\Installer\Http\Middleware\Authenticate;
use DreamFactory\Enterprise\Installer\Http\Middleware\EncryptCookies;
use DreamFactory\Enterprise\Installer\Http\Middleware\RedirectIfAuthenticated;
use DreamFactory\Enterprise\Installer\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /** @inheritdoc */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
    ];

    /** @inheritdoc */
    protected $routeMiddleware = [
        'auth'       => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'guest'      => RedirectIfAuthenticated::class,
    ];
}
