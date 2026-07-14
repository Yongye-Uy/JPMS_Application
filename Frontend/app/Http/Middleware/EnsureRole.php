<?php

namespace App\Http\Middleware;

use App\Support\AuthenticatedUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Real per-route role enforcement — the React prototype defined
 * ProtectedRoute.tsx for this but never wired it into the router, so any
 * authenticated user could navigate straight to /dashboard/admin/users.
 * This middleware (usage: role:Editor,Admin — any-of) plus Backend's own
 * server-side RBAC checks close that gap for real.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! AuthenticatedUser::check()) {
            return redirect()->route('auth.login');
        }

        // Laravel splits "role:A,B,C" into separate middleware parameters
        // (not one comma-joined string), so `$roles` here is already the
        // full list — do not re-explode on ',' or only the first role
        // would ever be checked (this exact bug was found and fixed in
        // Backend's copy of this same middleware — see
        // Backend/app/Http/Middleware/EnsureRole.php).
        if (! array_intersect($roles, AuthenticatedUser::roleNames())) {
            return redirect()->route('access-denied');
        }

        return $next($request);
    }
}
