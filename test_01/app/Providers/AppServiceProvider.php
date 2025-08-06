<?php

namespace App\Providers;

use App\Services\DeployApp;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $this->app->when(DeployApp::class)->needs('$githubToken')->give(fn() => config('githib.token'));
    }
}
