<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Editor Dashboard</h1>
    </div>

    {{-- Search & status filter bar --}}
    <div class="flex gap-3 items-center">
        <div class="relative flex-1 min-w-0">
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search title / abstract…"
                class="field text-sm"
            >
            <div wire:loading wire:target="search,status,nextPage,prevPage,gotoPage" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>
        <div class="shrink-0 w-44">
            <select wire:model.live="status" class="field text-sm">
                <option value="">All Statuses</option>
                <option value="Submitted">Submitted</option>
                <option value="Under Review">Under Review</option>
                <option value="Ready for Decision">Ready for Decision</option>
                <option value="Revision Required">Revision Required</option>
                <option value="Accepted">Accepted</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="card" wire:loading.class="opacity-50" wire:target="search,status,nextPage,prevPage,gotoPage">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-muted-foreground">
                    <th class="p-3 font-medium">Title</th>
                    <th class="p-3 font-medium">Journal</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Submitted</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($manuscripts as $m)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $m['title'] }}</td>
                        <td class="p-3 text-muted-foreground">{{ $m['journal']['title'] ?? '—' }}</td>
                        <td class="p-3"><span class="badge">{{ $m['status'] }}</span></td>
                        <td class="p-3 text-muted-foreground">{{ !empty($m['submitted_at']) ? \Illuminate\Support\Carbon::parse($m['submitted_at'])->toFormattedDateString() : '—' }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('editor.submissions.show', ['submissionId' => $m['id']]) }}" class="text-primary hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-3 text-muted-foreground">No submissions found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($lastPage > 1)
            <div class="flex items-center justify-between px-3 py-3 border-t text-sm">
                <span class="text-muted-foreground">Page {{ $page }} of {{ $lastPage }} &middot; {{ $total }} total</span>
                <div class="flex items-center gap-1">
                    <button wire:click="prevPage" @disabled($page <= 1)
                        class="px-2 py-1 rounded border text-xs disabled:opacity-40 hover:bg-muted transition-colors">
                        &larr;
                    </button>
                    @php
                        $start = max(1, $page - 2);
                        $end   = min($lastPage, $page + 2);
                    @endphp
                    @if ($start > 1)
                        <button wire:click="gotoPage(1)" class="px-2 py-1 rounded border text-xs hover:bg-muted transition-colors">1</button>
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
                        <button wire:click="gotoPage({{ $lastPage }})" class="px-2 py-1 rounded border text-xs hover:bg-muted transition-colors">{{ $lastPage }}</button>
                    @endif
                    <button wire:click="nextPage" @disabled($page >= $lastPage)
                        class="px-2 py-1 rounded border text-xs disabled:opacity-40 hover:bg-muted transition-colors">
                        &rarr;
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
