<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-semibold mb-1">Search Articles</h1>
        <p class="text-muted-foreground text-sm">Search the published archive by title, author, keyword, or abstract.</p>
    </div>

    <div class="card p-6">
        <form wire:submit="search" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1">Field</label>
                <select wire:model.live="field" class="field w-full">
                    <option value="title">Title</option>
                    <option value="author">Author</option>
                    <option value="keyword">Keyword</option>
                    <option value="abstract">Abstract</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1">Search term</label>
                <input type="text" wire:model.live.debounce.400ms="q" class="field w-full" placeholder="e.g. machine learning">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Journal</label>
                <select wire:model.live="journal_id" class="field w-full">
                    <option value="">All journals</option>
                    @foreach ($journals as $j)
                        <option value="{{ $j['id'] }}">{{ $j['title'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Year</label>
                <input type="text" wire:model.live.debounce.400ms="year" class="field w-full" placeholder="2026">
            </div>
            <div class="md:col-span-5">
                <button type="submit" class="btn-primary">Search</button>
            </div>
        </form>
    </div>

    @if ($searched)
        <div class="space-y-3">
            <p class="text-sm text-muted-foreground">
                {{ $total }} result(s)
                @if ($lastPage > 1)
                    — page {{ $page }} of {{ $lastPage }}
                @endif
            </p>
            @forelse ($results as $article)
                <a href="{{ route('public.article-detail', ['articleId' => $article['id']]) }}"
                    class="block card p-5 hover:border-primary/40">
                    <h2 class="font-semibold">{{ $article['manuscript']['title'] ?? 'Untitled' }}</h2>
                    <p class="text-sm text-muted-foreground mt-1">
                        {{ $article['issue']['journal']['title'] ?? '' }}
                        @if (!empty($article['issue']))
                            — Vol {{ $article['issue']['volume'] }}({{ $article['issue']['number'] }}), {{ $article['issue']['year'] }}
                        @endif
                    </p>
                    <p class="text-sm text-muted-foreground mt-2 line-clamp-2">{{ $article['manuscript']['abstract'] ?? '' }}</p>
                </a>
            @empty
                <p class="text-muted-foreground text-sm">No articles found.</p>
            @endforelse

            @if ($lastPage > 1)
                <div class="flex items-center justify-between pt-2">
                    <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
                        @disabled($page <= 1) class="btn-outline btn-sm">Previous</button>
                    <div class="flex items-center gap-1">
                        @foreach (range(max(1, $page - 2), min($lastPage, $page + 2)) as $p)
                            <button type="button" wire:click="gotoPage({{ $p }})" wire:loading.attr="disabled"
                                class="btn-sm {{ $p === $page ? 'btn-primary' : 'btn-outline' }}">{{ $p }}</button>
                        @endforeach
                    </div>
                    <button type="button" wire:click="nextPage" wire:loading.attr="disabled"
                        @disabled($page >= $lastPage) class="btn-outline btn-sm">Next</button>
                </div>
            @endif
        </div>
    @endif
</div>
