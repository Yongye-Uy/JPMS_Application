<div class="max-w-xl space-y-6">
    <div class="mb-2">
        <a href="{{ route('editor.submissions.show', ['submissionId' => $submissionId]) }}" class="text-sm text-muted-foreground hover:text-foreground inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Back to Manuscript
        </a>
    </div>
    @if ($notFound)
        <div class="card p-6 text-muted-foreground">Manuscript not found.</div>
    @else
        <h1 class="text-xl font-semibold">Editorial Decision — {{ $manuscript['title'] }}</h1>

        @if ($locked)
            <div class="rounded bg-muted text-foreground text-sm px-3 py-2">This manuscript is not awaiting an editorial decision — it may already have been decided, or hasn't been submitted for review yet.</div>
        @endif

        @if ($error)
            <div class="alert-error">{{ $error }}</div>
        @endif

        <form wire:submit="submit" class="card p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Decision</label>
                <select wire:model="decision" @disabled($locked) class="field w-full">
                    <option value="">Select…</option>
                    @foreach (self::DECISIONS as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>
                @error('decision') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Decision Letter</label>
                <textarea wire:model="decision_letter" @disabled($locked) rows="5" class="field w-full"></textarea>
                @error('decision_letter') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-muted-foreground mt-1">Required for Return to Edit / Minor / Major Revision.</p>
            </div>

            @unless ($locked)
                <button type="submit" class="btn-primary w-full">Submit Decision</button>
            @endunless
        </form>
    @endif
</div>
