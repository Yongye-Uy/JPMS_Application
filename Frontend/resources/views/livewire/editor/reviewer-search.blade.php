<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Find Reviewers</h1>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search name/affiliation…" class="field text-sm">
    </div>

    @if ($inviteMessage)
        <div class="alert-success">{{ $inviteMessage }}</div>
    @endif

    <div class="card divide-y">
        @forelse ($reviewers as $reviewer)
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-sm">{{ $reviewer['full_name'] }}</p>
                        <p class="text-xs text-muted-foreground">{{ $reviewer['email'] }} — {{ $reviewer['affiliation'] }}</p>
                    </div>
                    <button wire:click="startInvite({{ $reviewer['id'] }})" class="btn-outline btn-sm">Invite</button>
                </div>

                @if ($invitingReviewerId === $reviewer['id'])
                    <div class="mt-3 pt-3 border-t space-y-2">
                        @if ($inviteError)
                            <div class="alert-error">{{ $inviteError }}</div>
                        @endif
                        <div class="flex items-end gap-2">
                            <div class="flex-1">
                                <label class="block text-xs text-muted-foreground mb-1">Manuscript</label>
                                <select wire:model="manuscript_id" class="field text-sm w-full">
                                    <option value="">Select a manuscript…</option>
                                    @foreach ($manuscripts as $m)
                                        <option value="{{ $m['id'] }}">{{ $m['title'] }} — {{ $m['journal']['title'] ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-muted-foreground mb-1">Review deadline</label>
                                <input type="date" wire:model="deadline" class="field text-sm">
                            </div>
                            <button wire:click="invite" class="btn-primary btn-sm">Send Invitation</button>
                        </div>
                        @error('manuscript_id') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        @error('deadline') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>
        @empty
            <div class="p-4 text-sm text-muted-foreground">No reviewers found.</div>
        @endforelse
    </div>
</div>
