<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">User Management <span class="text-sm font-normal text-muted-foreground ml-2">({{ $total }} total)</span></h1>
        <div class="flex gap-2">
            <div class="flex gap-1">
                <input type="text" wire:model="search" wire:keydown.enter="performSearch" placeholder="Search name/email…" class="field text-sm">
                <button wire:click="performSearch" class="btn-primary btn-sm">Search</button>
            </div>
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
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'" wire:model="new_password" placeholder="Password" class="field text-sm w-full pr-8">
                    <button type="button" @click="show = !show" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-cloak x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
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
    @php $lastPage = $page + (count($users) < $perPage ? 0 : 1); @endphp
    <div class="flex items-center justify-between mt-4">
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
