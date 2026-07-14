<?php

namespace App\Providers;

use App\Auth\RemoteUser;
use App\Clients\ApiClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        /**
         * "remote-sanctum" guard: resolves the Bearer token on every request
         * to a RemoteUser by asking API to introspect it (which forwards to
         * Central-Service). Cached in Backend's OWN Redis for ~60s so most
         * requests don't pay the network round trip — this is exactly what
         * the architecture diagram's "Application Server <-> Redis" arrows
         * represent (session/token cache, distinct from Central-Service's
         * jpms:* data cache).
         */
        Auth::viaRequest('remote-sanctum', function (Request $request) {
            $token = $request->bearerToken();

            if (! $token) {
                return null;
            }

            $cacheKey = 'token:'.hash('sha256', $token);

            $payload = Cache::remember($cacheKey, 60, function () use ($token) {
                $response = app(ApiClient::class)->post('/auth/tokens/introspect', ['token' => $token]);

                return $response->successful() ? $response->json() : null;
            });

            if (! $payload || ! ($payload['user']['is_active'] ?? false)) {
                return null;
            }

            $user = RemoteUser::fromApiPayload($payload);
            \App\Support\CurrentActor::set($user->id);

            return $user;
        });
    }
}
