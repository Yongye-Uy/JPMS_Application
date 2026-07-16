<div class="space-y-6">
    @php $currentUserId = \App\Support\AuthenticatedUser::id(); @endphp

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Role Management</h1>
        @if ($this->hasChanges())
            <div class="flex gap-3">
                <button type="button" wire:click="resetChanges" wire:loading.attr="disabled" wire:target="save"
                    class="btn-outline btn-sm">Reset Changes</button>
                <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                    class="btn-primary btn-sm">Save Changes</button>
            </div>
        @endif
    </div>

    @if ($error)
        <div class="alert-error">{{ $error }}</div>
    @endif

    @if ($successMessage)
        <div class="alert-success" x-data x-init="setTimeout(() => $wire.successMessage = '', 3000)">{{ $successMessage }}</div>
    @endif

    @if ($ownRolesChanged)
        {{-- Your own roles changed: the sidebar lives in the surrounding layout and
             only picks up the refreshed session on a real page load, so reload once
             the success message has had a moment to show. --}}
        <div x-data x-init="setTimeout(() => window.location.reload(), 1200)"></div>
    @endif

    <div class="card p-6">
        <p class="text-sm text-muted-foreground mb-4">
            Assign or revoke roles for users. Changes are saved when you click "Save Changes".
        </p>

        <div class="space-y-4">
            @foreach ($users as $user)
                @php
                    $rowHasChanges = $this->userHasChanges($user['id']);
                    $rowHasNoRoles = collect($pendingRoles[$user['id']] ?? [])->doesntContain(true);
                @endphp
                <div wire:key="user-row-{{ $user['id'] }}"
                     class="rounded-lg border p-4 {{ $rowHasChanges ? 'border-primary/40 bg-primary/5' : 'border-border' }}">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div>
                            <p class="font-medium">{{ $user['full_name'] }}</p>
                            <p class="text-sm text-muted-foreground">{{ $user['email'] }}</p>
                            @if ($user['id'] === $currentUserId)
                                <p class="text-xs text-primary mt-1">(You)</p>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @foreach (self::ROLES as $role)
                                <label wire:key="role-{{ $user['id'] }}-{{ $role }}-{{ $rejectionNonce }}" class="flex items-center gap-2 text-sm">
                                    <input type="checkbox"
                                        @checked($pendingRoles[$user['id']][$role] ?? false)
                                        wire:click="toggleRole({{ $user['id'] }}, '{{ $role }}')">
                                    {{ $role }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @if ($rowHasNoRoles)
                        <p class="text-sm text-destructive mt-2">Warning: User has no roles assigned</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @php $lastPage = $page + (count($users) < $perPage ? 0 : 1); @endphp
    <div class="flex items-center justify-between my-4">
        <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
            @disabled($page <= 1) class="btn-outline btn-sm">Previous</button>
        <div class="flex items-center gap-1">
            @foreach (range(max(1, $page - 2), min($lastPage, $page + 2)) as $p)
                <button type="button" wire:click="goToPage({{ $p }})" wire:loading.attr="disabled"
                    @disabled($p === $page)
                    class="btn-sm {{ $p === $page ? 'btn-primary' : 'btn-outline' }}">{{ $p }}</button>
            @endforeach
        </div>
        <button type="button" wire:click="nextPage" wire:loading.attr="disabled"
            @disabled($page >= $lastPage) class="btn-outline btn-sm">Next</button>
    </div>
</div>
