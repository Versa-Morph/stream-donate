<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;

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

        // Rate-limit OTP verification attempts
        // Max 5 attempts per hour per IP
        RateLimiter::for('otp-verify', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Rate-limit OTP resend requests
        // Max 3 resends per 10 minutes per IP
        RateLimiter::for('otp-resend', function (Request $request) {
            return Limit::perMinutes(10, 3)->by($request->ip());
        });

        // Rate-limit login attempts
        // Max 5 attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate-limit password reset requests
        // Max 3 requests per hour per IP
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Rate-limit API key regeneration
        // Max 3 times per hour per user
        RateLimiter::for('api-key-regen', function (Request $request) {
            return Limit::perHour(3)->by($request->user()?->id ?? $request->ip());
        });

        // Rate-limit test alert sending
        // Max 10 per minute per user (for testing purposes)
        RateLimiter::for('test-alert', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?? $request->ip());
        });

        // Rate-limit report exports
        // Max 5 exports per minute per user (prevents abuse)
        RateLimiter::for('report-export', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?? $request->ip());
        });

        // Rate-limit settings updates
        // Max 10 updates per minute per user
        RateLimiter::for('settings-update', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?? $request->ip());
        });

        // Rate-limit admin user management actions
        // Max 20 actions per minute per admin
        RateLimiter::for('admin-actions', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?? $request->ip());
        });

        // Rate-limit SSE connections per IP
        // Max 10 concurrent connections per IP (per minute window)
        RateLimiter::for('sse', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Rate-limit OBS widget access
        // Max 120 requests per minute per IP (widget auto-refresh)
        RateLimiter::for('obs-widget', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Rate-limit QR code generation
        // Max 30 requests per minute per IP
        RateLimiter::for('qr-code', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Rate-limit stats endpoint (public API)
        // Max 60 requests per minute per IP
        RateLimiter::for('stats-api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Rate-limit heatmap data (AJAX)
        // Max 30 requests per minute per user
        RateLimiter::for('heatmap', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?? $request->ip());
        });

        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        if ($this->app->environment('production')) {
            Artisan::call('migrate', ['--force' => true]);
        }
    }
}
