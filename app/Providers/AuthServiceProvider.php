<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Eherkenning\OrganizationAuthGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        Auth::extend('org', function ($app, $name, array $config) {
            return new OrganizationAuthGuard($app->make('session')->driver(), $app->make('events'));
        });
    }
}
