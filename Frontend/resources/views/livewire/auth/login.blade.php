<div>
    <h1 class="text-xl font-semibold mb-4">Log in</h1>

    @if ($error)
        <div class="mb-4 alert-error">{{ $error }}</div>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" wire:model="email" class="field w-full" autofocus>
            @error('email') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" wire:model="password" class="field w-full">
            @error('password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
            Log in
        </button>
    </form>

    <p class="text-sm text-muted-foreground mt-4 text-center">
        No account? <a href="{{ route('auth.register') }}" class="underline">Register</a>
    </p>
</div>
