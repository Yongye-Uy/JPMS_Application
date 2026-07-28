<div class="max-w-2xl space-y-6">
    <div class="mb-2">
        <a href="{{ route('editor.submissions.show', ['submissionId' => $review['invitation']['manuscript']['id'] ?? 0]) }}" class="text-sm text-muted-foreground hover:text-foreground inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Back to Manuscript
        </a>
    </div>
    <h1 class="text-xl font-semibold">Review Detail</h1>

    @if ($notFound)
        <div class="card p-6 text-muted-foreground">Review not found.</div>
    @else
        <div class="card p-6 space-y-4">
            <div>
                <p class="text-sm font-medium">Reviewer</p>
                <p class="text-sm">{{ $review['invitation']['reviewer']['full_name'] ?? '' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium">Manuscript</p>
                <p class="text-sm">{{ $review['invitation']['manuscript']['title'] ?? '' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium mb-2">Scores</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    @foreach ($review['scores'] ?? [] as $score)
                        <div class="flex justify-between border-b py-1">
                            <span>{{ $score['criterion'] }}</span>
                            <span class="font-medium">{{ $score['score'] }}/5</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="text-sm font-medium">Recommendation</p>
                <p class="text-sm">{{ $review['recommendation'] ?? '—' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium">Comments to Author</p>
                <p class="text-sm text-foreground">{{ $review['comments_to_author'] ?? '—' }}</p>
            </div>

            @if (!empty($review['comments_to_editor']))
                <div>
                    <p class="text-sm font-medium">Confidential Comments to Editor</p>
                    <p class="text-sm text-foreground">{{ $review['comments_to_editor'] }}</p>
                </div>
            @endif

            @foreach ($review['files'] ?? [] as $f)
                @php $reviewFileUrl = route('files.show', ['path' => 'reviews/'.$review['id'].'/files/'.$f['id'].'/download']); @endphp
                <x-pdf-view-button :url="$reviewFileUrl" label="View annotated file" class="inline-block text-sm text-primary hover:underline" />
            @endforeach
        </div>
    @endif
</div>
