<div class="max-w-2xl space-y-6">
    <h1 class="text-2xl font-semibold">New Submission</h1>

    @if ($error)
        <div class="alert-error">{{ $error }}</div>
    @endif

    <div class="card p-6">
        <form wire:submit="submit" class="space-y-4">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium">Journal</label>
                    <button type="button" wire:click="$toggle('showNewJournal')" class="text-xs text-primary hover:underline">
                        + New Journal
                    </button>
                </div>

                @if ($showNewJournal)
                    <div class="mb-2 p-3 rounded border border-border bg-muted/30 space-y-2">
                        @if ($newJournalError) <div class="alert-error">{{ $newJournalError }}</div> @endif
                        <input type="text" wire:model="new_journal_title" placeholder="Journal title" class="field w-full text-sm">
                        @error('new_journal_title') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        <button type="button" wire:click="createJournal" class="btn-primary btn-sm">Create Journal</button>
                    </div>
                @endif

                @if (empty($journals))
                    <p class="text-sm text-destructive">No journals are available to submit to right now — use "+ New Journal" above to add one.</p>
                @else
                    <select wire:model="journal_id" class="field w-full">
                        <option value="">Select a journal&hellip;</option>
                        @foreach ($journals as $j)
                            <option value="{{ $j['id'] }}">{{ $j['title'] }}</option>
                        @endforeach
                    </select>
                @endif
                @error('journal_id') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Manuscript type</label>
                <select wire:model="manuscript_type" class="field w-full">
                    <option>Research Article</option>
                    <option>Review Article</option>
                    <option>Case Study</option>
                    <option>Short Communication</option>
                    <option>Letter to Editor</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Title</label>
                <input type="text" wire:model="title" class="field w-full">
                @error('title') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Abstract</label>
                <textarea wire:model="abstract" rows="5" class="field w-full"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Keywords (comma-separated)</label>
                <input type="text" wire:model="keywords" class="field w-full" placeholder="e.g. genomics, plants, drought">
            </div>

            <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                Save as Draft
            </button>
        </form>
    </div>
</div>
