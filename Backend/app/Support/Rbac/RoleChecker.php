<?php

namespace App\Support\Rbac;

use App\Auth\RemoteUser;

/**
 * Central place for "does this user hold this role" checks, used by Gates
 * and Policies. Never trusts a client-supplied "active role" — always
 * checks the full role-grant set Central-Service returned for the token.
 */
class RoleChecker
{
    public function hasRole(RemoteUser $user, string $role, ?int $journalId = null): bool
    {
        return $user->hasRole($role, $journalId);
    }

    public function hasAnyRole(RemoteUser $user, array $roles, ?int $journalId = null): bool
    {
        foreach ($roles as $role) {
            if ($user->hasRole($role, $journalId)) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(RemoteUser $user): bool
    {
        return $user->hasRole('Admin');
    }
}
