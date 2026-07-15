<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Editor Dashboard</h1>
    </div>

    {{-- Search & status filter bar --}}
    <div class="flex gap-3">
        <div class="relative flex-1">
            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search title / abstract…"
                class="field text-sm w-full pl-9"
            >
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.197 5.197a7.5 7.5 0 0 0 10.606 10.606Z"/>
            </svg>
            <div wire:loading class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>
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

    <div class="card" wire:loading.class="opacity-50">
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
    </div>
</div>
