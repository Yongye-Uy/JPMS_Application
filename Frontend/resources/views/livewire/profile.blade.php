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
            <div>
                <label class="block text-sm font-medium mb-1">Current password</label>
                <input type="password" wire:model="old_password" class="field w-full">
                @error('old_password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">New password</label>
                <input type="password" wire:model="new_password" class="field w-full">
                @error('new_password') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm new password</label>
                <input type="password" wire:model="new_password_confirmation" class="field w-full">
                @error('new_password_confirmation') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary">Change password</button>
        </form>
    </div>
</div>
