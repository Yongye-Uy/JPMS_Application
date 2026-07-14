<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;

/**
 * Cached copy of the logged-in user's profile + role grants, stored in
 * Frontend's own server-side session (not the browser) alongside the
 * Backend token. Refreshed on login and whenever roles change; never the
 * source of truth for authorization — Backend re-checks roles on every
 * write, this is purely for UI rendering (sidebar, route guards for UX).
 */
class AuthenticatedUser
{
    public static function check(): bool
    {
        return Session::has('backend_token') && Session::has('auth_user');
    }

    public static function id(): ?int
    {
        return Session::get('auth_user.id');
    }

    public static function fullName(): ?string
    {
        return Session::get('auth_user.full_name');
    }

    public static function email(): ?string
    {
        return Session::get('auth_user.email');
    }

    /** @return array<int, array{role_name:string, journal_id:?int}> */
    public static function roles(): array
    {
        return Session::get('auth_user.roles', []);
    }

    public static function roleNames(): array
    {
        return array_values(array_unique(array_column(self::roles(), 'role_name')));
    }

    public static function hasRole(string $role, ?int $journalId = null): bool
    {
        foreach (self::roles() as $grant) {
            if ($grant['role_name'] !== $role) {
                continue;
            }
            if (($grant['journal_id'] ?? null) === null) {
                return true;
            }
            if ($journalId !== null && $grant['journal_id'] === $journalId) {
                return true;
            }
        }

        return false;
    }

    public static function store(array $user): void
    {
        Session::put('auth_user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'roles' => array_map(
                fn ($r) => ['role_name' => $r['role_name'], 'journal_id' => $r['pivot']['journal_id'] ?? null],
                $user['roles'] ?? []
            ),
        ]);
    }

    public static function clear(): void
    {
        Session::forget(['backend_token', 'auth_user']);
    }

    /** Highest-priority role for default post-login redirect (Admin > Editor > Reviewer > Author > Reader). */
    public static function primaryRole(): ?string
    {
        $priority = ['Admin', 'Editor', 'Reviewer', 'Author', 'Reader'];
        $owned = self::roleNames();

        foreach ($priority as $role) {
            if (in_array($role, $owned, true)) {
                return $role;
            }
        }

        return null;
    }
}
