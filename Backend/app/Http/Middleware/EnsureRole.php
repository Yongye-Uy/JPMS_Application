<?php

namespace App\Http\Middleware;

use App\Auth\RemoteUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route middleware: role:Editor or role:Editor,Admin (any-of). Journal
 * scoping (e.g. "Editor of *this* journal") is enforced separately by
 * Policies where the journal_id is only known from the route-bound
 * resource, not the route definition — see app/Policies/*.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        /** @var RemoteUser|null $user */
        $user = Auth::guard('remote-sanctum')->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Laravel splits "role:A,B,C" into three separate middleware
        // parameters (not one comma-joined string), so `$roles` here is
        // already the full list — do not re-explode on ',' or only the
        // first role would ever be checked.
        if (! array_intersect($roles, $user->roleNames())) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
