<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Facades\Octane;

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
        // Octane::tick(
        //     'simple-ticker', fn () => Log::info("OCTANE TICk", ['timestamp' => now()])
        // )->seconds(10)->immediate();

        Octane::tick('cache-last-random-number', function() {
            $number = rand(1, 1000);
            Cache::store('octane')->put('last-random-number', $number);
            Log::info("New number in cache: " . $number, ['timestamp' => now()]);
            return;
        })->seconds(10)->immediate();
    }
}
