<?php

namespace App\Http\Middleware;

use App\Support\AuthenticatedUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! AuthenticatedUser::check()) {
            session(['return_to' => $request->fullUrl()]);

            return redirect()->route('auth.login');
        }

        // Always ensure the session has the absolute latest roles/profile data.
        // This is a fast internal call to the Backend.
        try {
            $backend = app(\App\Clients\BackendClient::class);
            $response = $backend->get('/profile');
            if ($response->successful()) {
                AuthenticatedUser::store($response->json());
            }
        } catch (\Exception $e) {
            // If backend is down, we just proceed with the cached session
        }

        return $next($request);
    }
}
