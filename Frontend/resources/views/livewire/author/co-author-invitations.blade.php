<div class="space-y-6">
    <h1 class="text-xl font-semibold">Co-Author Invitations</h1>

    @if ($message)
        <div class="alert-success">{{ $message }}</div>
    @endif
    @if ($error)
        <div class="alert-error">{{ $error }}</div>
    @endif

    <div class="card divide-y">
        @forelse ($invitations as $inv)
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="font-medium text-sm">{{ $inv['manuscript']['title'] ?? '' }}</p>
                    <p class="text-xs text-muted-foreground">
                        Invited by {{ $inv['inviting_author']['full_name'] ?? '' }} — {{ $inv['manuscript']['journal']['title'] ?? '' }}
                    </p>
                    <span class="badge">{{ $inv['status'] }}</span>
                </div>
                @if ($inv['status'] === 'Pending')
                    <div class="flex gap-2">
                        <button wire:click="respond({{ $inv['id'] }}, 'Accepted')" class="btn-primary btn-sm">Accept</button>
                        <button wire:click="respond({{ $inv['id'] }}, 'Declined')" class="btn-outline btn-sm">Decline</button>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-4 text-sm text-muted-foreground">No co-author invitations.</div>
        @endforelse
    </div>
</div>
