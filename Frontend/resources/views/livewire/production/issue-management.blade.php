<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Issue Management</h1>
        <a href="{{ route('production.issues.create') }}" class="btn-primary btn-sm">Create New Issue</a>
    </div>

    {{-- Search & filter bar --}}
    <div class="flex gap-3 items-center">
        <div class="relative flex-1 min-w-0">
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search by journal, volume, year…"
                class="field text-sm"
            >
            <div wire:loading wire:target="search,statusFilter,nextPage,prevPage,gotoPage" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>
        <div class="shrink-0 w-36">
            <select wire:model.live="statusFilter" class="field text-sm">
                <option value="">All Statuses</option>
                <option value="Draft">Draft</option>
                <option value="Published">Published</option>
            </select>
        </div>
    </div>

    <div class="card" wire:loading.class="opacity-50" wire:target="search,statusFilter,nextPage,prevPage,gotoPage">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-muted-foreground">
                    <th class="p-3 font-medium">Journal</th>
                    <th class="p-3 font-medium">Volume</th>
                    <th class="p-3 font-medium">Number</th>
                    <th class="p-3 font-medium">Year</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($issues as $issue)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $issue['journal']['title'] ?? '' }}</td>
                        <td class="p-3">{{ $issue['volume'] }}</td>
                        <td class="p-3">{{ $issue['number'] }}</td>
                        <td class="p-3">{{ $issue['year'] }}</td>
                        <td class="p-3"><span class="badge">{{ $issue['status'] }}</span></td>
                        <td class="p-3 text-right">
                            <a href="{{ route('production.issues.manage', ['issueId' => $issue['id']]) }}" class="text-primary hover:underline">Manage</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-3 text-muted-foreground">No issues found.</td></tr>
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
</div>
