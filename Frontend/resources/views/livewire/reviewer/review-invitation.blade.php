<div class="max-w-2xl space-y-6">
    <h1 class="text-xl font-semibold">Review Invitation</h1>

    @if ($notFound)
        <div class="card p-6 text-muted-foreground">Invitation not found.</div>
    @else
        @if ($error)
            <div class="alert-error">{{ $error }}</div>
        @endif

        <div class="card p-6 space-y-3">
            <h2 class="font-medium text-lg">{{ $invitation['manuscript']['title'] ?? '' }}</h2>
            <p class="text-sm text-muted-foreground">{{ $invitation['manuscript']['journal']['title'] ?? '' }} — {{ $invitation['manuscript']['manuscript_type'] ?? '' }}</p>
            <p class="text-sm">Deadline: <strong>{{ \Illuminate\Support\Carbon::parse($invitation['deadline'])->toFormattedDateString() }}</strong></p>
            <div>
                <p class="text-sm font-medium mb-1">Abstract</p>
                <p class="text-sm text-foreground">{{ $invitation['manuscript']['abstract'] ?? '' }}</p>
            </div>
            @php
                $mainFile = collect($invitation['manuscript']['current_version']['files'] ?? [])->firstWhere('file_type', 'main');
                $mainFileUrl = $mainFile ? route('files.show', ['path' => "manuscripts/{$invitation['manuscript']['id']}/files/{$mainFile['id']}/download"]) : null;
            @endphp
            @if ($mainFile)
                <x-pdf-view-button :url="$mainFileUrl" label="View manuscript PDF" class="inline-block text-sm text-primary hover:underline" />
            @endif
        </div>

        @if (($invitation['status'] ?? '') === 'Pending')
            <div class="card p-6 space-y-4">
                <button wire:click="accept" class="btn-primary">Accept Invitation</button>

                <div class="pt-4 border-t">
                    <label class="block text-sm font-medium mb-1">Decline reason</label>
                    <textarea wire:model="declined_reason" rows="3" class="field w-full"></textarea>
                    @error('declined_reason') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                    <button wire:click="decline" class="mt-2 btn-outline btn-sm">Decline</button>
                </div>
            </div>
        @else
            <div class="rounded bg-muted text-foreground text-sm px-3 py-2">
                This invitation is already <strong>{{ $invitation['status'] }}</strong>.
            </div>
        @endif
    @endif
</div>
