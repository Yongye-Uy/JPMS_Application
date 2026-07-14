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

        return $next($request);
    }
}
