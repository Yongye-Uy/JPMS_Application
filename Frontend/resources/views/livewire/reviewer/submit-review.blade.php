<div class="max-w-2xl space-y-6">
    <h1 class="text-xl font-semibold">Submit Review</h1>

    @if ($notFound)
        <div class="card p-6 text-muted-foreground">Invitation not found.</div>
    @else
        @php
            $mainFile = collect($invitation['manuscript']['current_version']['files'] ?? [])->firstWhere('file_type', 'main');
            $mainFileUrl = $mainFile ? route('files.show', ['path' => "manuscripts/{$invitation['manuscript']['id']}/files/{$mainFile['id']}/download"]) : null;
        @endphp

        <div class="card p-6">
            <h2 class="font-medium">{{ $invitation['manuscript']['title'] ?? '' }}</h2>
            <p class="text-sm text-muted-foreground mt-1">Deadline: {{ \Illuminate\Support\Carbon::parse($invitation['deadline'])->toFormattedDateString() }}</p>
            @if ($mainFile)
                <x-pdf-view-button :url="$mainFileUrl" label="View manuscript PDF" class="inline-block mt-2 text-sm text-primary hover:underline" />
            @endif
        </div>

        @if ($locked)
            <div class="rounded bg-muted text-foreground text-sm px-3 py-2">This review has already been submitted and is read-only.</div>
        @endif

        @if ($error)
            <div class="alert-error">{{ $error }}</div>
        @endif

        <form wire:submit="submit" class="card p-6 space-y-6">
            <div>
                <p class="text-sm font-medium mb-3">Scores (0–5)</p>
                <div class="space-y-2">
                    @foreach (self::CRITERIA as $criterion)
                        <div class="flex items-center justify-between">
                            <label class="text-sm">{{ $criterion }}</label>
                            <select wire:model="scores.{{ $criterion }}" @disabled($locked) class="field">
                                @for ($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Recommendation</label>
                <select wire:model="recommendation" @disabled($locked) class="field w-full">
                    <option value="">Select…</option>
                    @foreach (self::RECOMMENDATIONS as $rec)
                        <option value="{{ $rec }}">{{ $rec }}</option>
                    @endforeach
                </select>
                @error('recommendation') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Comments to Author</label>
                <textarea wire:model="comments_to_author" @disabled($locked) rows="4" class="field w-full"></textarea>
                @error('comments_to_author') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Confidential Comments to Editor (optional)</label>
                <textarea wire:model="comments_to_editor" @disabled($locked) rows="3" class="field w-full"></textarea>
            </div>

            @unless ($locked)
                <div>
                    <label class="block text-sm font-medium mb-1">Annotated file (optional)</label>
                    <label class="flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-border hover:border-primary hover:bg-primary/5 transition-colors px-6 py-8 cursor-pointer text-center">
                        <input type="file" wire:model="annotated_file" class="sr-only">
                        <svg class="w-8 h-8 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 8.25 12 3.75m0 0L7.5 8.25M12 3.75v13.5" />
                        </svg>
                        <span class="text-sm font-medium">Click to upload or drag and drop</span>
                        @if ($annotated_file)
                            <span class="text-xs text-primary mt-1">Selected: {{ $annotated_file->getClientOriginalName() }}</span>
                        @endif
                    </label>
                    @error('annotated_file') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
                    Submit Review
                </button>
            @endunless
        </form>
    @endif
</div>
