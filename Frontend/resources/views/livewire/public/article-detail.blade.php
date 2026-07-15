<div class="space-y-6 max-w-4xl">
    @if ($notFound)
        <div class="text-center py-16">
            <h1 class="text-xl font-semibold mb-2">Article not found</h1>
            <a href="{{ route('public.home') }}" class="underline text-sm">Back to search</a>
        </div>
    @else
        @php
            $manuscript = $article['manuscript'] ?? [];
            $issue = $article['issue'] ?? [];
            $journal = $issue['journal'] ?? [];
            $authors = collect($manuscript['authors'] ?? [])->sortBy('author_order')->pluck('user.full_name')->filter()->implode(', ');
            $year = $issue['year'] ?? '';
            $citation = trim(($authors ?: 'Unknown Author')." ({$year}). {$manuscript['title']}. {$journal['title']}, {$issue['volume']}({$issue['number']}), {$article['page_start']}-{$article['page_end']}.");
        @endphp

        <div>
            <a href="{{ route('public.home') }}" class="text-sm text-muted-foreground hover:underline">&larr; Back to search</a>
            <h1 class="text-2xl font-semibold mt-2">{{ $manuscript['title'] ?? '' }}</h1>
            <p class="text-muted-foreground mt-1">{{ $authors }}</p>
            <p class="text-sm text-muted-foreground mt-1">
                {{ $journal['title'] ?? '' }} — Vol {{ $issue['volume'] ?? '?' }}({{ $issue['number'] ?? '?' }}), {{ $year }},
                pp. {{ $article['page_start'] ?? '?' }}&ndash;{{ $article['page_end'] ?? '?' }}
            </p>
            @if (!empty($article['doi']))
                <p class="text-sm text-muted-foreground">DOI: {{ $article['doi'] }}</p>
            @endif
        </div>

        <div class="card p-6">
            <h2 class="font-medium mb-2">Abstract</h2>
            <p class="text-foreground text-sm">{{ $manuscript['abstract'] ?? 'No abstract available.' }}</p>
            @if (!empty($manuscript['keywords']))
                <div class="flex flex-wrap gap-2 mt-4">
                    @foreach ($manuscript['keywords'] as $kw)
                        <span class="text-xs bg-muted rounded-full px-3 py-1">{{ $kw['keyword_text'] }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card p-6">
            <h2 class="font-medium mb-3">Full text (PDF)</h2>
            <embed type="application/pdf" src="{{ route('files.show', ['path' => "articles/{$articleId}/view"]) }}#toolbar=0&navpanes=0" class="w-full h-[600px] border rounded">

            <div class="flex items-center gap-3 mt-4">
                @if ($this->isLoggedIn())
                    <a href="{{ route('files.show', ['path' => "articles/{$articleId}/download"]) }}" target="_blank"
                        x-data="" @click="setTimeout(() => $wire.refreshMetrics(), 1500)"
                        class="btn-primary btn-sm">Download PDF</a>
                @else
                    <a href="{{ route('auth.login') }}" class="btn-primary btn-sm">Log in to download</a>
                @endif

                <button type="button"
                    x-data="{ copied: false }"
                    @click="
                        (async () => {
                            const text = @js($citation);
                            try {
                                if (navigator.clipboard && window.isSecureContext) {
                                    await navigator.clipboard.writeText(text);
                                } else {
                                    const ta = document.createElement('textarea');
                                    ta.value = text;
                                    ta.style.position = 'fixed';
                                    ta.style.opacity = '0';
                                    document.body.appendChild(ta);
                                    ta.focus();
                                    ta.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(ta);
                                }
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                                $wire.trackCitation();
                            } catch (e) {
                                console.error('Copy failed', e);
                            }
                        })()
                    "
                    class="btn-outline btn-sm">
                    <span x-text="copied ? 'Copied!' : 'Copy Citation'">Copy Citation</span>
                </button>
            </div>

            <p class="text-xs text-muted-foreground mt-3 italic">{{ $citation }}</p>
        </div>

        <div class="card p-6">
            <h2 class="font-medium mb-3">Stats</h2>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-2xl font-semibold">{{ $article['views'] ?? 0 }}</p>
                    <p class="text-xs text-muted-foreground uppercase">Views</p>
                </div>
                <div>
                    <p class="text-2xl font-semibold">{{ $article['downloads'] ?? 0 }}</p>
                    <p class="text-xs text-muted-foreground uppercase">Downloads</p>
                </div>
                <div>
                    <p class="text-2xl font-semibold">{{ $article['citations_count'] ?? 0 }}</p>
                    <p class="text-xs text-muted-foreground uppercase">Citations</p>
                </div>
            </div>
        </div>
    @endif
</div>
