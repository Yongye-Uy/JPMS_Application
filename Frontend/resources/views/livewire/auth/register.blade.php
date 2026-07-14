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
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" wire:model="password" class="field w-full">
                @error('password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm password</label>
                <input type="password" wire:model="password_confirmation" class="field w-full">
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
