<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">User Management</h1>
        <div class="flex gap-2">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search name/email…" class="field text-sm">
            <button wire:click="$toggle('showCreate')" class="btn-primary btn-sm">Create User</button>
        </div>
    </div>

    @if ($showCreate)
        <div class="card p-6 space-y-3">
            <h2 class="font-medium">New User</h2>
            @if ($createError)
                <div class="alert-error">{{ $createError }}</div>
            @endif
            <div class="grid grid-cols-2 gap-2">
                <input type="text" wire:model="new_full_name" placeholder="Full name" class="field text-sm">
                <input type="email" wire:model="new_email" placeholder="Email" class="field text-sm">
                <input type="password" wire:model="new_password" placeholder="Password" class="field text-sm">
            </div>
            <div class="flex gap-3 text-sm">
                @foreach (self::ROLES as $role)
                    <label class="flex items-center gap-1">
                        <input type="checkbox" wire:model="new_roles" value="{{ $role }}"> {{ $role }}
                    </label>
                @endforeach
            </div>
            <button wire:click="createUser" class="btn-primary btn-sm">Create</button>
        </div>
    @endif

    <div class="card divide-y">
        @foreach ($users as $user)
            <div class="p-4" wire:key="user-row-{{ $user['id'] }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-sm">{{ $user['full_name'] }} @unless($user['is_active']) <span class="text-destructive text-xs">(inactive)</span> @endunless</p>
                        <p class="text-xs text-muted-foreground">{{ $user['email'] }}</p>
                        <div class="mt-1 flex gap-1 flex-wrap">
                            @foreach ($user['roles'] as $r)
                                <span class="badge">{{ $r['role_name'] }}</span>
                            @endforeach
                        </div>
                    </div>
                    <button wire:click="startEdit({{ $user['id'] }})" class="text-sm text-primary hover:underline">Edit</button>
                </div>

                @if ($editingUserId === $user['id'])
                    <div class="mt-3 pt-3 border-t space-y-3">
                        @if ($editError)
                            <div class="alert-error">{{ $editError }}</div>
                        @endif
                        <div class="flex gap-2 items-center">
                            <input type="text" wire:model="edit_full_name" class="field text-sm">
                            <label class="flex items-center gap-1 text-sm">
                                <input type="checkbox" wire:model="edit_is_active"> Active
                            </label>
                            <button wire:click="saveEdit" class="btn-primary btn-sm">Save</button>
                        </div>
                        <div class="flex gap-3 text-sm">
                            @foreach (self::ROLES as $role)
                                @php $has = collect($user['roles'])->contains('role_name', $role); @endphp
                                <label class="flex items-center gap-1" wire:key="edit-role-{{ $user['id'] }}-{{ $role }}-{{ $roleToggleNonce }}">
                                    <input type="checkbox" @checked($has) wire:click="toggleRole({{ $user['id'] }}, '{{ $role }}', {{ $has ? 'true' : 'false' }})"> {{ $role }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
