<div>
    <h1 class="text-xl font-semibold mb-4">Create account</h1>

    @if ($error)
        <div class="mb-4 alert-error">{{ $error }}</div>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div class="grid grid-cols-2 gap-2">
            <button type="button" wire:click="$set('accountType', 'Reader')"
                class="rounded border px-3 py-2 text-sm {{ $accountType === 'Reader' ? 'border-primary bg-muted' : 'border-input' }}">
                Reader
            </button>
            <button type="button" wire:click="$set('accountType', 'Author')"
                class="rounded border px-3 py-2 text-sm {{ $accountType === 'Author' ? 'border-primary bg-muted' : 'border-input' }}">
                Author
            </button>
        </div>
        <p class="text-xs text-muted-foreground">Reviewer/Editor/Admin accounts are granted by an administrator, not self-registered.</p>

        <div>
            <label class="block text-sm font-medium mb-1">Full name</label>
            <input type="text" wire:model="full_name" class="field w-full">
            @error('full_name') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" wire:model="email" class="field w-full">
            @error('email') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm font-medium mb-1">Affiliation</label>
                <input type="text" wire:model="affiliation" class="field w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Country</label>
                <input type="text" wire:model="country" class="field w-full">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Contact info</label>
            <input type="text" wire:model="contact_info" class="field w-full">
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium mb-1">Password</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="password" class="field w-full pr-10">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-cloak x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                @error('password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
            <div x-data="{ show: false }">
                <label class="block text-sm font-medium mb-1">Confirm password</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" wire:model="password_confirmation" class="field w-full pr-10">
                    <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-cloak x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                @error('password_confirmation') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
            Create account
        </button>
    </form>

    <p class="text-sm text-muted-foreground mt-4 text-center">
        Already have an account? <a href="{{ route('auth.login') }}" class="underline">Log in</a>
    </p>
</div>
