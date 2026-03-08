<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Rate-limit the public donation submission endpoint.
        // Allows 5 donations per minute per IP+slug combination.
        RateLimiter::for('donate', function (Request $request) {
            $slug = $request->route('slug') ?? 'unknown';
            return Limit::perMinute(5)->by($request->ip() . '|' . $slug);
        });
    }
}
