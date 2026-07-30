<div class="space-y-6" wire:init="performSearch">
    <div class="mb-2">
        <a href="{{ route('editor.dashboard') }}" class="text-sm text-muted-foreground hover:text-foreground inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Back to Dashboard
        </a>
    </div>
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Find Reviewers</h1>
        <div class="flex gap-2 items-center">
            <span class="text-sm text-muted-foreground">{{ $total }} reviewers found</span>
            <div class="flex gap-1">
                <input type="text" wire:model="search" wire:keydown.enter="performSearch" placeholder="Search name/affiliation…" class="field text-sm">
                <button wire:click="performSearch" class="btn-primary btn-sm">Search</button>
            </div>
        </div>
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
                                <input type="date" wire:model="deadline" min="{{ \Carbon\Carbon::tomorrow()->toDateString() }}" class="field text-sm">
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
    
    @if ($lastPage > 1)
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
    @endif
</div>
