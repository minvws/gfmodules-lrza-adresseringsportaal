<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Eherkenning\OrganizationAuthGuard;
use App\Services\HapiService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->singleton(HapiService::class, function (Application $app) {
            return new HapiService(config('hapi.endpoint'));
        });

        $this->bootAuth();
    }

    public function bootAuth(): void
    {
        Auth::extend('org', function (Application $app, string $name, array $config) {
            return new OrganizationAuthGuard($app->make('session')->driver(), $app->make('events'));
        });
    }
}
