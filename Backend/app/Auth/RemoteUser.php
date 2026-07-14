<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Backend has no local users table — this wraps the user + role-grant data
 * that Central-Service returned for the current Bearer token, resolved by
 * RemoteSanctumGuard (see AppServiceProvider::boot()).
 */
class RemoteUser implements Authenticatable
{
    /** @param array<int, array{id:int, role_name:string, journal_id:?int}> $roles */
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $fullName,
        public readonly bool $isActive,
        public readonly array $roles,
        public readonly int $tokenId,
    ) {}

    public static function fromApiPayload(array $payload): self
    {
        $user = $payload['user'];

        return new self(
            id: $user['id'],
            email: $user['email'],
            fullName: $user['full_name'],
            isActive: $user['is_active'],
            roles: array_map(
                fn ($r) => ['role_name' => $r['role_name'], 'journal_id' => $r['pivot']['journal_id'] ?? null],
                $user['roles'] ?? []
            ),
            tokenId: $payload['token_id'],
        );
    }

    /** True if the user holds $role either globally or scoped to $journalId. */
    public function hasRole(string $role, ?int $journalId = null): bool
    {
        foreach ($this->roles as $grant) {
            if ($grant['role_name'] !== $role) {
                continue;
            }
            if ($grant['journal_id'] === null) {
                return true; // global grant (e.g. Admin)
            }
            if ($journalId !== null && $grant['journal_id'] === $journalId) {
                return true;
            }
        }

        return false;
    }

    public function roleNames(): array
    {
        return array_values(array_unique(array_column($this->roles, 'role_name')));
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void {}

    public function getRememberTokenName(): string
    {
        return '';
    }
}
