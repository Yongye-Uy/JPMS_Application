<?php

namespace App\Livewire\Admin;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Role Management')]
class RoleManagement extends Component
{
    public const ROLES = ['Reader', 'Author', 'Reviewer', 'Editor', 'Admin'];

    /** Last-fetched server snapshot: array<int, array{id, full_name, email, roles: array<array{id, role_name}>}> */
    public array $users = [];
    public int $perPage = 25;
    public int $page = 1;

    /** Staged/local edits only: array<userId, array<roleName, bool>>. Never sent to Backend until save(). */
    public array $pendingRoles = [];

    public string $error = '';
    public string $successMessage = '';

    /**
     * Set by save() when the currently-logged-in admin changed their own
     * roles. The sidebar is part of the surrounding layout, not this
     * component, so a Livewire re-render alone never touches it — the Blade
     * view uses this flag to force a full page reload, refreshing the
     * sidebar against the now-updated session (mirrors the Figma
     * reference's own window.location.reload() for this exact case).
     */
    public bool $ownRolesChanged = false;

    /**
     * Bumped whenever toggleRole() rejects an attempt. Folded into the
     * checkbox wire:key so Livewire fully remounts the checkboxes instead of
     * morphing them in place — a plain morph leaves the browser's own
     * (unwanted) native checked-state toggle on screen even though
     * $pendingRoles never changed, since morphdom preserves live form-control
     * state across a patch by design.
     */
    public int $rejectionNonce = 0;

    public function mount(BackendClient $backend): void
    {
        $this->loadUsers($backend);
    }

    private function loadUsers(BackendClient $backend): void
    {
        $response = $backend->get('/users', ['per_page' => $this->perPage, 'page' => $this->page]);
        $this->users = $response->successful() ? ($response->json('data') ?? []) : [];
        $this->syncPendingFromUsers();
    }

    private function syncPendingFromUsers(): void
    {
        $this->pendingRoles = collect($this->users)->mapWithKeys(function (array $user) {
            $owned = collect($user['roles'])->pluck('role_name')->all();

            return [
                $user['id'] => collect(self::ROLES)
                    ->mapWithKeys(fn ($role) => [$role => in_array($role, $owned, true)])
                    ->all(),
            ];
        })->all();
    }

    public function toggleRole(int $userId, string $role): void
    {
        $this->error = '';
        $this->ownRolesChanged = false;

        $wantsToRevokeOwnAdmin = $role === 'Admin'
            && $userId === AuthenticatedUser::id()
            && ($this->pendingRoles[$userId][$role] ?? false);

        if ($wantsToRevokeOwnAdmin) {
            $this->error = 'You cannot revoke your own Admin role.';
            $this->rejectionNonce++;

            return;
        }

        $this->pendingRoles[$userId][$role] = ! ($this->pendingRoles[$userId][$role] ?? false);
    }

    public function userHasChanges(int $userId): bool
    {
        $user = collect($this->users)->firstWhere('id', $userId);
        $owned = collect($user['roles'] ?? [])->pluck('role_name')->all();

        foreach (self::ROLES as $role) {
            if (($this->pendingRoles[$userId][$role] ?? false) !== in_array($role, $owned, true)) {
                return true;
            }
        }

        return false;
    }

    public function hasChanges(): bool
    {
        foreach ($this->users as $user) {
            if ($this->userHasChanges($user['id'])) {
                return true;
            }
        }

        return false;
    }

    public function resetChanges(): void
    {
        $this->error = '';
        $this->successMessage = '';
        $this->ownRolesChanged = false;
        $this->syncPendingFromUsers();
    }

    public function save(BackendClient $backend): void
    {
        $this->error = '';
        $this->ownRolesChanged = false;

        $currentUserId = AuthenticatedUser::id();
        $selfChanged = $currentUserId !== null && $this->userHasChanges($currentUserId);

        foreach ($this->users as $user) {
            $userId = $user['id'];
            $owned = collect($user['roles']);

            foreach (self::ROLES as $role) {
                $hadRole = $owned->contains('role_name', $role);
                $wantsRole = $this->pendingRoles[$userId][$role] ?? false;

                if ($hadRole === $wantsRole) {
                    continue;
                }

                if ($wantsRole) {
                    $backend->post("/users/{$userId}/roles", ['role_name' => $role]);
                } else {
                    $grant = $owned->firstWhere('role_name', $role);
                    $backend->delete("/users/{$userId}/roles/{$grant['id']}");
                }
            }
        }

        $this->loadUsers($backend);
        $this->successMessage = 'Role changes saved successfully.';

        // The sidebar reads AuthenticatedUser::roleNames() from the session,
        // cached at login — a plain Livewire re-render never touches it,
        // since the sidebar lives in the surrounding layout, not this
        // component. Refresh the session immediately so the data is correct,
        // and let the Blade view force a full reload so it's visible too.
        if ($selfChanged) {
            $freshSelf = collect($this->users)->firstWhere('id', $currentUserId);
            if ($freshSelf) {
                AuthenticatedUser::store($freshSelf);
            }
            $this->ownRolesChanged = true;
        }
    }

    public function previousPage(BackendClient $backend): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->loadUsers($backend);
        }
    }

    public function nextPage(BackendClient $backend): void
    {
        if (count($this->users) === $this->perPage) {
            $this->page++;
            $this->loadUsers($backend);
        }
    }

    public function render()
    {
        return view('livewire.admin.role-management');
    }
}
