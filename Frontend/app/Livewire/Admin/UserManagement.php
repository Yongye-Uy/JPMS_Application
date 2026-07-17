<?php

namespace App\Livewire\Admin;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('User Management')]
class UserManagement extends Component
{
    public string $search = '';
    public array $users = [];
    public int $perPage = 25;
    public int $page = 1;

    public bool $showCreate = false;
    public string $new_email = '';
    public string $new_password = '';
    public string $new_full_name = '';
    public array $new_roles = [];
    public string $createError = '';

    public ?int $editingUserId = null;
    public string $edit_full_name = '';
    public bool $edit_is_active = true;
    public string $editError = '';

    /**
     * Bumped only when toggleRole() fails. Folded into the role-checkbox
     * wire:key so a failed toggle forces Livewire to remount the checkbox
     * instead of morphing it in place — otherwise the browser's own native
     * checked-state toggle stays on screen even though nothing was actually
     * saved (morphdom preserves live form-control state across a patch by
     * design; see the same pattern/reasoning in RoleManagement.php).
     */
    public int $roleToggleNonce = 0;

    public const ROLES = ['Reader', 'Author', 'Reviewer', 'Editor', 'Admin'];

    public function mount(BackendClient $backend)
    {
        $this->load($backend);
    }

    public function updatedSearch(BackendClient $backend)
    {
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get('/users', array_filter(['search' => $this->search, 'per_page' => $this->perPage, 'page' => $this->page]));
        $this->users = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function createUser(BackendClient $backend)
    {
        $this->createError = '';

        $this->validate([
            'new_email' => 'required|email',
            'new_password' => 'required|string|min:6',
            'new_full_name' => 'required|string',
        ]);

        $roles = array_map(fn ($r) => ['role_name' => $r, 'journal_id' => null], $this->new_roles);

        $response = $backend->post('/users', [
            'email' => $this->new_email,
            'password' => $this->new_password,
            'full_name' => $this->new_full_name,
            'roles' => $roles,
        ]);

        if (! $response->successful()) {
            $this->createError = $response->json('message') ?? 'Could not create user.';

            return;
        }

        $this->reset(['new_email', 'new_password', 'new_full_name', 'new_roles', 'showCreate']);
        $this->load($backend);
    }

    public function startEdit(int $userId)
    {
        $user = collect($this->users)->firstWhere('id', $userId);
        $this->editingUserId = $userId;
        $this->edit_full_name = $user['full_name'] ?? '';
        $this->edit_is_active = $user['is_active'] ?? true;
        $this->editError = '';
    }

    public function saveEdit(BackendClient $backend)
    {
        $this->editError = '';

        if ($this->editingUserId === AuthenticatedUser::id() && ! $this->edit_is_active) {
            $this->editError = 'You cannot deactivate your own account.';

            return;
        }

        $response = $backend->patch("/users/{$this->editingUserId}", [
            'full_name' => $this->edit_full_name,
            'is_active' => $this->edit_is_active,
        ]);

        if (! $response->successful()) {
            $this->editError = $response->json('message') ?? 'Could not update user.';

            return;
        }

        $this->editingUserId = null;
        $this->load($backend);
    }

    public function toggleRole(int $userId, string $role, bool $has, BackendClient $backend)
    {
        $this->editError = '';

        if ($has) {
            $user = collect($this->users)->firstWhere('id', $userId);
            $grant = collect($user['roles'] ?? [])->firstWhere('role_name', $role);
            $response = $grant ? $backend->delete("/users/{$userId}/roles/{$grant['id']}") : null;
        } else {
            $response = $backend->post("/users/{$userId}/roles", ['role_name' => $role]);
        }

        if ($response && ! $response->successful()) {
            $this->editError = $response->json('message') ?? 'Could not update role.';
            $this->roleToggleNonce++;
        }

        $this->load($backend);
    }

    public function previousPage(BackendClient $backend): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->load($backend);
        }
    }

    public function nextPage(BackendClient $backend): void
    {
        if (count($this->users) === $this->perPage) {
            $this->page++;
            $this->load($backend);
        }
    }

    public function goToPage(int $page): void
    {
        $this->page = $page;
        $backend = app(BackendClient::class);
        $this->load($backend);
    }


    public function render()
    {
        return view('livewire.admin.user-management');
    }
}
