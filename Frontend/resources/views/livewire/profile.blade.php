<div class="space-y-8 max-w-lg">
    <h1 class="text-xl font-semibold">Profile</h1>

    <div class="card p-6">
        <h2 class="font-medium mb-4">Account details</h2>

        @if ($profileMessage)
            <div class="mb-4 alert-success">{{ $profileMessage }}</div>
        @endif

        <form wire:submit="updateProfile" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Full name</label>
                <input type="text" wire:model="full_name" class="field w-full">
                @error('full_name') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Affiliation</label>
                <input type="text" wire:model="affiliation" class="field w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Country</label>
                <input type="text" wire:model="country" class="field w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Contact info</label>
                <input type="text" wire:model="contact_info" class="field w-full">
            </div>
            <button type="submit" class="btn-primary">Save</button>
        </form>
    </div>

    <div class="card p-6">
        <h2 class="font-medium mb-4">Change password</h2>

        @if ($passwordMessage)
            <div class="mb-4 alert-success">{{ $passwordMessage }}</div>
        @endif
        @if ($passwordError)
            <div class="mb-4 alert-error">{{ $passwordError }}</div>
        @endif

        <form wire:submit="changePassword" class="space-y-4">
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium mb-1">Current password</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="old_password" class="field w-full pr-10">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-cloak x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                @error('old_password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium mb-1">New password</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="new_password" class="field w-full pr-10">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-cloak x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                @error('new_password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium mb-1">Confirm new password</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="new_password_confirmation" class="field w-full pr-10">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-cloak x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                @error('new_password_confirmation') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary">Change password</button>
        </form>
    </div>
</div>
