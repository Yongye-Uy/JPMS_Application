<div class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Production Dashboard</h1>
        <a href="{{ route('production.issues') }}" class="btn-primary btn-sm">Manage Issues</a>
    </div>

    {{-- Search bar (no icon) --}}
    <div class="relative">
        <input
            type="text"
            wire:model.live.debounce.400ms="search"
            placeholder="Search accepted manuscripts by title…"
            class="field text-sm"
        >
        <div wire:loading wire:target="search,nextPage,prevPage,gotoPage" class="absolute right-3 top-1/2 -translate-y-1/2">
            <svg class="animate-spin h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>
    </div>

    {{-- Accepted — not yet in an issue --}}
    <div class="card" wire:loading.class="opacity-50" wire:target="search,nextPage,prevPage,gotoPage">
        <div class="p-4 border-b font-medium flex items-center justify-between">
            <span>Accepted — not yet in an issue</span>
            <span class="text-xs text-muted-foreground">{{ count($unassigned) }} on this page</span>
        </div>
        <table class="w-full text-sm">
            <tbody>
                @forelse ($unassigned as $m)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $m['title'] }}</td>
                        <td class="p-3 text-muted-foreground">{{ $m['journal']['title'] ?? '' }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('production.issues') }}" class="text-primary hover:underline">Add to Issue</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-muted-foreground">None.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($lastPage > 1)
            <div class="flex items-center justify-between px-3 py-3 border-t text-sm">
                <span class="text-muted-foreground">Page {{ $page }} of {{ $lastPage }} &middot; {{ $total }} total</span>
                <div class="flex items-center gap-1">
                    <button wire:click="prevPage" @disabled($page <= 1)
                        class="px-2 py-1 rounded border text-xs disabled:opacity-40 hover:bg-muted transition-colors">&larr;</button>
                    @php $start = max(1, $page - 2); $end = min($lastPage, $page + 2); @endphp
                    @if ($start > 1)
                        <button wire:click="gotoPage(1)" class="px-2 py-1 rounded border text-xs hover:bg-muted">1</button>
                        @if ($start > 2) <span class="px-1 text-muted-foreground">…</span> @endif
                    @endif
                    @for ($i = $start; $i <= $end; $i++)
                        <button wire:click="gotoPage({{ $i }})"
                            class="px-2 py-1 rounded border text-xs transition-colors {{ $i === $page ? 'bg-primary text-primary-foreground border-primary' : 'hover:bg-muted' }}">
                            {{ $i }}
                        </button>
                    @endfor
                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1) <span class="px-1 text-muted-foreground">…</span> @endif
                        <button wire:click="gotoPage({{ $lastPage }})" class="px-2 py-1 rounded border text-xs hover:bg-muted">{{ $lastPage }}</button>
                    @endif
                    <button wire:click="nextPage" @disabled($page >= $lastPage)
                        class="px-2 py-1 rounded border text-xs disabled:opacity-40 hover:bg-muted transition-colors">&rarr;</button>
                </div>
            </div>
        @endif
    </div>

    {{-- In a Draft Issue --}}
    <div class="card">
        <div class="p-4 border-b font-medium flex items-center justify-between">
            <span>In a Draft Issue</span>
            <span class="text-xs text-muted-foreground">{{ count($inDraftIssues) }} on this page</span>
        </div>
        <table class="w-full text-sm">
            <tbody>
                @forelse ($inDraftIssues as $row)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $row['manuscript']['title'] }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('production.issues.manage', ['issueId' => $row['article']['issue_id']]) }}" class="text-primary hover:underline">Open Issue</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-muted-foreground">None.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
