<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Only Backend is allowed to call this API. */
class EnsureBackendToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Internal-Token');

        if (! $token || ! hash_equals((string) config('services.backend.shared_secret'), (string) $token)) {
            return response()->json(['message' => 'Invalid internal token.'], 401);
        }

        return $next($request);
    }
}
