<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Config;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustHosts(at: function () {
            return Config::array('app.trusted_hosts');
        }, subdomains: false);
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\Locale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
$app->useEnvironmentPath(getenv('APP_ENV_PATH') ?: '');
return $app;
