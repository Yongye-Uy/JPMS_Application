<div class="max-w-3xl space-y-6">
    @if ($notFound)
        <div class="card p-6 text-muted-foreground">Manuscript not found.</div>
    @else
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-semibold">{{ $manuscript['title'] }}</h1>
                <p class="text-sm text-muted-foreground">{{ $manuscript['journal']['title'] ?? '' }} — <span class="badge">{{ $manuscript['status'] }}</span></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('editor.reviews.monitor', ['submissionId' => $submissionId]) }}" class="btn-outline btn-sm">Manage Reviews</a>
                @if (in_array($manuscript['status'] ?? null, ['Submitted', 'Under Review', 'Ready for Decision'], true))
                    <a href="{{ route('editor.decision', ['submissionId' => $submissionId]) }}" class="btn-primary btn-sm">Make Decision</a>
                @endif
            </div>
        </div>

        <div class="card p-6">
            <p class="text-sm font-medium mb-1">Abstract</p>
            <p class="text-sm text-foreground">{{ $manuscript['abstract'] }}</p>
        </div>

        @php
            $currentVersion = collect($manuscript['versions'] ?? [])->firstWhere('id', $manuscript['current_version_id'] ?? null);
            $mainFile = collect($currentVersion['files'] ?? [])->firstWhere('file_type', 'main');
            $mainFileUrl = $mainFile ? route('files.show', ['path' => "manuscripts/{$manuscript['id']}/files/{$mainFile['id']}/download"]) : null;
            $otherVersions = collect($manuscript['versions'] ?? [])->reject(fn ($v) => $v['id'] === ($manuscript['current_version_id'] ?? null));
        @endphp

        <div class="card p-6">
            <p class="text-sm font-medium mb-3">Current Main Manuscript</p>
            @if ($mainFile)
                <div class="flex items-center justify-between rounded border p-4">
                    <div>
                        <p class="font-medium">{{ $mainFile['original_filename'] }}</p>
                        <p class="text-sm text-muted-foreground">{{ $mainFile['size_kb'] }} KB</p>
                    </div>
                    <x-pdf-view-button :url="$mainFileUrl" label="View Current Main PDF" class="btn-primary btn-sm" />
                </div>
            @else
                <p class="text-muted-foreground text-sm">No main manuscript uploaded yet.</p>
            @endif
        </div>

        <div class="card p-6">
            <p class="text-sm font-medium mb-3">Version History</p>
            @if ($otherVersions->isEmpty())
                <p class="text-muted-foreground text-sm">No version history available.</p>
            @else
                <table class="w-full text-sm">
                    <tbody>
                        @foreach ($otherVersions as $version)
                            @php
                                $vFile = collect($version['files'] ?? [])->firstWhere('file_type', 'main');
                                $vFileUrl = $vFile ? route('files.show', ['path' => "manuscripts/{$manuscript['id']}/files/{$vFile['id']}/download"]) : null;
                            @endphp
                            <tr class="border-b last:border-0">
                                <td class="p-2">v{{ $version['version_number'] }}</td>
                                <td class="p-2 text-muted-foreground">{{ !empty($version['uploaded_at']) ? \Illuminate\Support\Carbon::parse($version['uploaded_at'])->toFormattedDateString() : '' }}</td>
                                <td class="p-2 text-muted-foreground">{{ $version['response_note'] ?? '' }}</td>
                                <td class="p-2 text-right">
                                    @if ($vFile)
                                        <x-pdf-view-button :url="$vFileUrl" label="View PDF" class="text-primary hover:underline" />
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="card p-6">
            <p class="text-sm font-medium mb-3">Completed Reviews</p>
            <table class="w-full text-sm">
                <tbody>
                    @forelse (($manuscript['review_invitations'] ?? []) as $inv)
                        @if (!empty($inv['review']))
                            <tr class="border-b last:border-0">
                                <td class="p-2">{{ $inv['reviewer']['full_name'] ?? '' }}</td>
                                <td class="p-2 text-muted-foreground">{{ $inv['review']['recommendation'] ?? '' }}</td>
                                <td class="p-2 text-right">
                                    <a href="{{ route('editor.review-detail', ['reviewId' => $inv['review']['id']]) }}" class="text-primary hover:underline">View</a>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td class="p-2 text-muted-foreground">No completed reviews yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @endif
</div>
