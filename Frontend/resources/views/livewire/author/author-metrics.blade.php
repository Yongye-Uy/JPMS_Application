<div class="space-y-6">
    <h1 class="text-xl font-semibold">My Metrics</h1>

    <div class="grid grid-cols-3 gap-4">
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Total Views</p>
            <p class="text-2xl font-semibold">{{ $this->totalViews() }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Total Downloads</p>
            <p class="text-2xl font-semibold">{{ $this->totalDownloads() }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Total Citations</p>
            <p class="text-2xl font-semibold">{{ $this->totalCitations() }}</p>
        </div>
    </div>

    <div class="card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-muted-foreground">
                    <th class="p-3 font-medium">Article</th>
                    <th class="p-3 font-medium">Views</th>
                    <th class="p-3 font-medium">Downloads</th>
                    <th class="p-3 font-medium">Citations</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($articles as $article)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $article['manuscript']['title'] ?? '' }}</td>
                        <td class="p-3">{{ $article['views'] ?? 0 }}</td>
                        <td class="p-3">{{ $article['downloads'] ?? 0 }}</td>
                        <td class="p-3">{{ $article['citations_count'] ?? 0 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-3 text-muted-foreground">No published articles yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
