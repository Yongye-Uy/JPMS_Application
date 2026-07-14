<div class="space-y-6 max-w-3xl">
    @if ($notFound)
        <div class="text-center py-16">
            <h1 class="text-xl font-semibold mb-2">Issue not found</h1>
            <a href="{{ route('public.browse-issues') }}" class="underline text-sm">Back to issues</a>
        </div>
    @else
        <div>
            <a href="{{ route('public.browse-issues') }}" class="text-sm text-muted-foreground hover:underline">&larr; Back to issues</a>
            <h1 class="text-2xl font-semibold mt-2">{{ $issue['journal']['title'] ?? '' }}</h1>
            <p class="text-muted-foreground">Volume {{ $issue['volume'] }}, Number {{ $issue['number'] }} ({{ $issue['year'] }})</p>
        </div>

        <div class="card p-6">
            <h2 class="font-medium mb-3">Table of Contents</h2>
            <ul class="divide-y">
                @forelse ($issue['articles'] ?? [] as $article)
                    <li class="py-3">
                        <a href="{{ route('public.article-detail', ['articleId' => $article['id']]) }}" class="font-medium hover:underline">
                            {{ $article['manuscript']['title'] ?? 'Untitled' }}
                        </a>
                        <p class="text-sm text-muted-foreground">pp. {{ $article['page_start'] }}&ndash;{{ $article['page_end'] }}</p>
                    </li>
                @empty
                    <p class="text-muted-foreground text-sm py-3">No articles in this issue yet.</p>
                @endforelse
            </ul>
        </div>
    @endif
</div>
