<div class="space-y-6 max-w-4xl">
    @if ($notFound)
        <div class="text-center py-16">
            <h1 class="text-xl font-semibold mb-2">Submission not found</h1>
            <a href="{{ route('author.submissions') }}" class="underline text-sm">Back to submissions</a>
        </div>
    @else
        <div>
            <a href="{{ route('author.submissions') }}" class="text-sm text-muted-foreground hover:underline">&larr; My Submissions</a>
            <div class="flex items-center justify-between mt-2">
                <h1 class="text-2xl font-semibold">{{ $manuscript['title'] ?? 'Untitled' }}</h1>
                <span class="text-xs rounded-full px-3 py-1 bg-muted">{{ $manuscript['status'] ?? 'Unknown' }}</span>
            </div>
            <p class="text-sm text-muted-foreground mt-1">{{ $manuscript['journal']['title'] ?? '' }} &middot; {{ $manuscript['manuscript_type'] ?? '' }}</p>
        </div>

        @if (($manuscript['status'] ?? null) === 'Revision Required')
            @php
                $lastDecision = collect($manuscript['editorial_decisions'] ?? [])->sortByDesc('decided_at')->first();
            @endphp
            @if ($lastDecision)
                <div class="rounded bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3">
                    <p class="font-medium">Editor's decision: {{ $lastDecision['decision'] }}</p>
                    <p class="mt-1">{{ $lastDecision['decision_letter'] }}</p>
                </div>
            @endif
        @endif

        @if ($submitMessage) <div class="alert-success">{{ $submitMessage }}</div> @endif
        @if ($submitError) <div class="alert-error">{{ $submitError }}</div> @endif

        <div class="flex gap-2 border-b">
            @foreach (['overview' => 'Overview', 'files' => 'Files', 'coauthors' => 'Co-Authors', 'reviews' => 'Reviews', 'history' => 'History'] as $key => $label)
                <button type="button" wire:click="$set('tab', '{{ $key }}')"
                    class="px-3 py-2 text-sm border-b-2 {{ $tab === $key ? 'border-primary font-medium' : 'border-transparent text-muted-foreground' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @if ($tab === 'overview')
            <div class="card p-6 space-y-4">
                <div>
                    <h2 class="font-medium mb-1">Abstract</h2>
                    <p class="text-sm text-foreground">{{ ($manuscript['abstract'] ?? null) ?: 'No abstract yet.' }}</p>
                </div>
                @if (!empty($manuscript['keywords']))
                    <div class="flex flex-wrap gap-2">
                        @foreach ($manuscript['keywords'] as $kw)
                            <span class="text-xs bg-muted rounded-full px-3 py-1">{{ $kw['keyword_text'] }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="flex flex-wrap gap-3 pt-4 border-t">
                    @if (in_array($manuscript['status'] ?? null, ['Draft', 'Revision Required']) && $this->isOwner())
                        @php $isRevision = ($manuscript['status'] ?? null) === 'Revision Required'; @endphp
                        <button type="button" wire:click="submitForReview" wire:confirm="{{ $isRevision ? 'Resubmit this manuscript for review?' : 'Submit this manuscript for review?' }}"
                            class="btn-primary btn-sm">{{ $isRevision ? 'Resubmit for Review' : 'Submit for Review' }}</button>
                    @endif
                </div>

                @if (($manuscript['status'] ?? null) === 'Draft' && $this->isOwner())
                    <div class="pt-4 border-t">
                        <h2 class="font-medium mb-2 text-destructive">Delete draft</h2>
                        @if ($deleteError) <div class="alert-error mb-2">{{ $deleteError }}</div> @endif
                        <button type="button" wire:click="deleteManuscript" wire:confirm="Delete this draft manuscript? This cannot be undone."
                            class="rounded border border-red-300 text-red-700 px-4 py-2 text-sm">Delete Draft</button>
                    </div>
                @endif

                @if (in_array($manuscript['status'] ?? null, ['Submitted', 'Under Review']) && $this->isOwner())
                    <div class="pt-4 border-t">
                        <h2 class="font-medium mb-2">Withdraw submission</h2>
                        @if ($withdrawError) <div class="alert-error mb-2">{{ $withdrawError }}</div> @endif
                        <form wire:submit="withdraw" class="space-y-2">
                            <textarea wire:model="withdraw_reason" rows="2" class="w-full field text-sm" placeholder="Reason for withdrawal (required)"></textarea>
                            @error('withdraw_reason') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            <button type="submit" wire:confirm="Are you sure you want to withdraw this manuscript?"
                                class="rounded border border-red-300 text-red-700 px-4 py-2 text-sm">Withdraw</button>
                        </form>
                    </div>
                @endif

                @if (($manuscript['status'] ?? null) === 'Withdrawn' && $this->isOwner())
                    <div class="pt-4 border-t">
                        <h2 class="font-medium mb-2">Resubmit</h2>
                        @if ($resubmitError) <div class="alert-error mb-2">{{ $resubmitError }}</div> @endif
                        <p class="text-sm text-muted-foreground mb-2">Reopen this manuscript as a Draft so you can edit it and submit it for review again.</p>
                        <button type="button" wire:click="resubmitManuscript" wire:confirm="Reopen this manuscript as a Draft?"
                            class="btn-primary btn-sm">Resubmit</button>
                    </div>
                @endif
            </div>
        @endif

        @if ($tab === 'files')
            <div class="card p-6 space-y-4">
                <h2 class="font-medium">Current Files</h2>
                @php
                    $currentVersion = collect($manuscript['versions'] ?? [])->firstWhere('id', $manuscript['current_version_id'] ?? null);
                    $currentFiles = collect($currentVersion['files'] ?? []);
                @endphp
                <ul class="divide-y text-sm">
                    @forelse ($currentFiles as $f)
                        @php $fUrl = route('files.show', ['path' => "manuscripts/{$manuscript['id']}/files/{$f['id']}/download"]); @endphp
                        <li class="py-2">
                            <p class="text-muted-foreground flex items-center gap-2">
                                {{ $f['original_filename'] }} ({{ $f['size_kb'] }} KB)
                                @if ($f['file_type'] === 'supplementary')
                                    <span class="text-xs bg-muted rounded-full px-2 py-0.5">Supplementary</span>
                                @endif
                                <x-pdf-view-button :url="$fUrl" label="View" />
                            </p>
                        </li>
                    @empty
                        <p class="text-muted-foreground">No files uploaded yet.</p>
                    @endforelse
                </ul>
                @if ($currentVersion && !empty($currentVersion['response_note']))
                    <p class="text-muted-foreground italic text-sm">"{{ $currentVersion['response_note'] }}"</p>
                @endif

                @if (($this->isOwner() || $this->isCoAuthor()) && in_array($manuscript['status'] ?? null, ['Draft', 'Revision Required']))
                    <div class="pt-4 border-t">
                        <h2 class="font-medium mb-2">Upload {{ ($manuscript['status'] ?? null) === 'Revision Required' ? 'revised' : 'new' }} file</h2>
                        @if ($uploadMessage) <div class="alert-success mb-2">{{ $uploadMessage }}</div> @endif
                        @if ($uploadError) <div class="alert-error mb-2">{{ $uploadError }}</div> @endif
                        <form wire:submit="uploadVersion" class="space-y-2">
                            <label class="flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-border hover:border-primary hover:bg-primary/5 transition-colors px-6 py-8 cursor-pointer text-center">
                                <input type="file" wire:model="main_file" accept="application/pdf" class="sr-only">
                                <svg class="w-8 h-8 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 8.25 12 3.75m0 0L7.5 8.25M12 3.75v13.5" />
                                </svg>
                                <span class="text-sm font-medium">Click to upload or drag and drop</span>
                                <span class="text-xs text-muted-foreground">PDF only</span>
                                @if ($main_file)
                                    <span class="text-xs text-primary mt-1">Selected: {{ $main_file->getClientOriginalName() }}</span>
                                @endif
                            </label>
                            @error('main_file') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                            <input type="text" wire:model="response_note" placeholder="Note (optional)" class="w-full field text-sm">
                            <button type="submit" wire:loading.attr="disabled" class="btn-primary btn-sm">Upload</button>
                        </form>
                    </div>
                @endif
            </div>
        @endif

        @if ($tab === 'coauthors')
            <div class="card p-6 space-y-4">
                <h2 class="font-medium">Co-authors</h2>
                <ul class="divide-y text-sm">
                    @foreach ($manuscript['authors'] ?? [] as $a)
                        <li class="py-2">{{ $a['user']['full_name'] }} {{ $a['is_corresponding'] ? '(corresponding)' : '' }}</li>
                    @endforeach
                </ul>

                @if (!empty($manuscript['co_author_invitations']))
                    <h2 class="font-medium pt-2">Pending invitations</h2>
                    <ul class="divide-y text-sm">
                        @foreach ($manuscript['co_author_invitations'] as $inv)
                            <li class="py-2 flex items-center justify-between">
                                <span>{{ $inv['invited_author']['full_name'] ?? $inv['invited_author_id'] }}</span>
                                <span class="text-xs text-muted-foreground">{{ $inv['status'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($this->isOwner())
                    <div class="pt-4 border-t space-y-2">
                        <h2 class="font-medium mb-2">Invite a co-author</h2>
                        @if ($coauthorMessage) <div class="alert-success">{{ $coauthorMessage }}</div> @endif
                        @if ($coauthorError) <div class="alert-error">{{ $coauthorError }}</div> @endif

                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                            <div class="flex gap-2">
                                <input type="text" wire:model="coauthor_search" x-on:focus="open = true" placeholder="Search by name/email" class="flex-1 field text-sm">
                                <button type="button" wire:click="searchCoAuthors" x-on:click="open = true" class="rounded border px-3 py-2 text-sm">Search</button>
                            </div>

                            @if (!empty($coauthor_results))
                                <ul x-show="open" x-cloak class="absolute z-10 mt-1 w-full bg-card border rounded shadow-lg divide-y text-sm">
                                    @foreach ($coauthor_results as $u)
                                        <li class="py-2 px-3 flex items-center justify-between">
                                            <span>{{ $u['full_name'] }} ({{ $u['email'] }})</span>
                                            <button type="button" wire:click="inviteCoAuthor({{ $u['id'] }})" class="text-xs btn-primary btn-sm">Invite</button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <details class="text-sm">
                            <summary class="text-xs text-muted-foreground cursor-pointer">Or enter a user ID directly</summary>
                            <div class="flex gap-2 mt-2">
                                <input type="number" wire:model="invited_author_id_manual" placeholder="User ID" class="flex-1 field text-sm">
                                <button type="button" wire:click="inviteCoAuthor" class="btn-primary btn-sm">Invite</button>
                            </div>
                        </details>
                    </div>
                @endif
            </div>
        @endif

        @if ($tab === 'reviews')
            <div class="card p-6">
                <h2 class="font-medium mb-3">Reviews</h2>
                <ul class="divide-y text-sm">
                    @forelse ($manuscript['review_invitations'] ?? [] as $inv)
                        <li class="py-3">
                            <p class="font-medium">Reviewer: {{ $inv['reviewer']['full_name'] ?? 'Reviewer #'.$inv['reviewer_id'] }}
                                <span class="text-xs text-muted-foreground">({{ $inv['status'] }})</span>
                            </p>
                            @if (!empty($inv['review']))
                                <p class="text-muted-foreground mt-1">Recommendation: {{ $inv['review']['recommendation'] }}</p>
                                <p class="text-muted-foreground">{{ $inv['review']['comments_to_author'] }}</p>
                                @foreach ($inv['review']['files'] ?? [] as $f)
                                    @php $reviewFileUrl = route('files.show', ['path' => 'reviews/'.$inv['review']['id'].'/files/'.$f['id'].'/download']); @endphp
                                    <x-pdf-view-button :url="$reviewFileUrl" label="View annotated file" class="inline-block mt-1 text-sm text-primary hover:underline" />
                                @endforeach
                            @endif
                        </li>
                    @empty
                        <p class="text-muted-foreground">No reviewers assigned yet.</p>
                    @endforelse
                </ul>
            </div>
        @endif

        @if ($tab === 'history')
            <div class="card p-6 space-y-6">
                <div>
                    <h2 class="font-medium mb-3">Version History</h2>
                    @if ($setMainMessage) <div class="alert-success mb-2">{{ $setMainMessage }}</div> @endif
                    @if ($setMainError) <div class="alert-error mb-2">{{ $setMainError }}</div> @endif
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-muted-foreground border-b">
                                <th class="p-2 font-medium">Version</th>
                                <th class="p-2 font-medium">File Name</th>
                                <th class="p-2 font-medium">Uploaded By</th>
                                <th class="p-2 font-medium">Uploaded Date</th>
                                <th class="p-2 font-medium">Response Note</th>
                                <th class="p-2 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($manuscript['versions'] ?? [] as $version)
                                @php
                                    $mainFile = collect($version['files'] ?? [])->firstWhere('file_type', 'main');
                                    $isCurrent = ($manuscript['current_version_id'] ?? null) === $version['id'];
                                    $mainFileUrl = $mainFile ? route('files.show', ['path' => "manuscripts/{$manuscript['id']}/files/{$mainFile['id']}/download"]) : null;
                                @endphp
                                <tr class="border-b last:border-0">
                                    <td class="p-2">v{{ $version['version_number'] }}</td>
                                    <td class="p-2">{{ $mainFile['original_filename'] ?? '—' }}</td>
                                    <td class="p-2">{{ $version['uploaded_by']['full_name'] ?? 'Unknown' }}</td>
                                    <td class="p-2">{{ \Illuminate\Support\Carbon::parse($version['uploaded_at'])->format('M j, Y g:ia') }}</td>
                                    <td class="p-2 italic">{{ $version['response_note'] ?? '' }}</td>
                                    <td class="p-2 text-right space-x-2 whitespace-nowrap">
                                        @if ($mainFile)
                                            <x-pdf-view-button :url="$mainFileUrl" label="View PDF" class="text-primary hover:underline" />
                                        @endif
                                        @if ($isCurrent)
                                            <span class="text-xs rounded-full px-2 py-0.5 bg-green-100 text-green-800">Current Main</span>
                                        @elseif ($this->isOwner() && in_array($manuscript['status'] ?? null, ['Draft', 'Revision Required']))
                                            <button type="button" wire:click="setMainVersion({{ $version['id'] }})" wire:confirm="Set v{{ $version['version_number'] }} as the main version?" class="text-primary hover:underline">Set as Main</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-2 text-muted-foreground">No versions uploaded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div>
                    <h2 class="font-medium mb-3">Submission Timeline</h2>
                    <ul class="space-y-2 text-sm">
                        <li><span class="font-medium">Submission Created</span> — {{ \Illuminate\Support\Carbon::parse($manuscript['created_at'])->format('M j, Y g:ia') }}</li>
                        @if (!empty($manuscript['submitted_at']))
                            <li><span class="font-medium">Submitted for Review</span> — {{ \Illuminate\Support\Carbon::parse($manuscript['submitted_at'])->format('M j, Y g:ia') }}</li>
                        @endif
                        @if (($manuscript['status'] ?? null) === 'Withdrawn')
                            <li>
                                <span class="font-medium">Submission Withdrawn</span> — {{ !empty($manuscript['withdrawn_at']) ? \Illuminate\Support\Carbon::parse($manuscript['withdrawn_at'])->format('M j, Y g:ia') : '' }}
                                <p class="text-muted-foreground italic">"{{ $manuscript['withdrawal_reason'] ?? '' }}"</p>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="pt-4 border-t">
                    <h2 class="font-medium mb-3">Editorial history</h2>
                    <ul class="divide-y text-sm">
                        @forelse ($manuscript['editorial_decisions'] ?? [] as $d)
                            <li class="py-3">
                                <p class="font-medium">{{ $d['decision'] }} <span class="text-xs text-muted-foreground">by {{ $d['editor']['full_name'] ?? '' }}</span></p>
                                <p class="text-muted-foreground">{{ $d['decision_letter'] }}</p>
                                <p class="text-xs text-muted-foreground">{{ \Illuminate\Support\Carbon::parse($d['decided_at'])->format('M j, Y') }}</p>
                            </li>
                        @empty
                            <p class="text-muted-foreground">No editorial decisions yet.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    @endif
</div>
