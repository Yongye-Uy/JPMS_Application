<div class="max-w-3xl space-y-6">
    @if ($notFound)
        <div class="card p-6 text-muted-foreground">Manuscript not found.</div>
    @else
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-semibold">{{ $manuscript['title'] }}</h1>
                <p class="text-sm text-muted-foreground">{{ $manuscript['journal']['title'] ?? '' }} — <span class="badge">{{ $manuscript['status'] }}</span></p>
            </div>
        </div>

        @if ($message)
            <div class="alert-success">{{ $message }}</div>
        @endif
        @if ($error)
            <div class="alert-error">{{ $error }}</div>
        @endif

        <div class="card p-6">
            <p class="text-sm font-medium mb-1">Abstract</p>
            <p class="text-sm text-foreground">{{ $manuscript['abstract'] }}</p>
        </div>

        <div class="card p-6">
            <p class="text-sm font-medium mb-3">Authors</p>
            <div class="space-y-3">
                <div class="border-b pb-3 last:border-0 last:pb-0">
                    <p class="font-medium text-sm">{{ $manuscript['author']['full_name'] ?? '' }} <span class="badge">Submitting Author</span></p>
                    <p class="text-xs text-muted-foreground">{{ $manuscript['author']['email'] ?? '' }}</p>
                </div>
                @foreach ($manuscript['authors'] ?? [] as $coauthor)
                    <div class="border-b pb-3 last:border-0 last:pb-0">
                        <p class="font-medium text-sm">{{ $coauthor['user']['full_name'] ?? '' }} <span class="badge">Co-Author</span></p>
                        <p class="text-xs text-muted-foreground">{{ $coauthor['user']['email'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card p-6">
            <p class="text-sm font-medium mb-3">Version History</p>
            <table class="w-full text-sm">
                <tbody>
                    @foreach ($manuscript['versions'] ?? [] as $version)
                        @php
                            $mainFile = collect($version['files'] ?? [])->firstWhere('file_type', 'main');
                            $mainFileUrl = $mainFile ? route('files.show', ['path' => "manuscripts/{$manuscript['id']}/files/{$mainFile['id']}/download"]) : null;
                        @endphp
                        <tr class="border-b last:border-0">
                            <td class="p-2">v{{ $version['version_number'] }}</td>
                            <td class="p-2 text-right">
                                @if ($mainFile)
                                    <x-pdf-view-button :url="$mainFileUrl" label="View PDF" class="text-primary hover:underline" />
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card p-6">
            <p class="text-sm font-medium mb-3">Editorial Decisions</p>
            <div class="space-y-2 text-sm">
                @forelse ($manuscript['editorial_decisions'] ?? [] as $d)
                    <div class="border-b pb-2 last:border-0">
                        <p><strong>{{ $d['decision'] }}</strong> — {{ $d['editor']['full_name'] ?? '' }}</p>
                        @if (!empty($d['decision_letter']))
                            <p class="text-muted-foreground">{{ $d['decision_letter'] }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-muted-foreground">No decisions yet.</p>
                @endforelse
            </div>
        </div>

        <div class="card p-6 space-y-4">
            <p class="text-sm font-medium">Admin Actions</p>
            <div>
                <textarea wire:model="return_reason" placeholder="Reason for returning to author" rows="2" class="w-full field text-sm"></textarea>
                @error('return_reason') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
                <button wire:click="returnToAuthor" class="mt-2 btn-outline btn-sm">Return to Author</button>
            </div>
            <div class="flex gap-2">
                @if ($manuscript['status'] !== 'Archived')
                    <button wire:click="archive" wire:confirm="Archive this manuscript?" class="btn-outline btn-sm text-destructive">Archive</button>
                @else
                    <button wire:click="restore" class="btn-outline btn-sm">Restore</button>
                @endif
            </div>
        </div>
    @endif
</div>
