<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // One simple limiter for everything, including login — kept
        // generous since this is a class project, not a public-facing
        // production service that needs brute-force defense tuning.
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->header('X-Actor-User-Id') ?: $request->ip());
        });
    }
}
