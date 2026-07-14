<div class="space-y-8">
    <h1 class="text-2xl font-semibold">Browse Issues</h1>

    @forelse ($journals as $entry)
        <div class="card p-6">
            <h2 class="font-semibold mb-3">{{ $entry['journal']['title'] }}</h2>
            <ul class="divide-y">
                @foreach ($entry['issues'] as $issue)
                    <li class="py-2 flex items-center justify-between">
                        <a href="{{ route('public.issue-detail', ['issueId' => $issue['id']]) }}" class="hover:underline">
                            Vol {{ $issue['volume'] }}, No. {{ $issue['number'] }} ({{ $issue['year'] }})
                        </a>
                        @if (!empty($issue['publication_date']))
                            <span class="text-xs text-muted-foreground">{{ \Illuminate\Support\Carbon::parse($issue['publication_date'])->format('M j, Y') }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @empty
        <p class="text-muted-foreground text-sm">No published issues yet.</p>
    @endforelse
</div>
