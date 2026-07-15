<div class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Production Dashboard</h1>
        <a href="{{ route('production.issues') }}" class="btn-primary btn-sm">Manage Issues</a>
    </div>

    {{-- Search bar --}}
    <div class="relative">
        <input
            type="text"
            wire:model.live.debounce.400ms="search"
            placeholder="Search accepted manuscripts by title…"
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

    <div class="card" wire:loading.class="opacity-50">
        <div class="p-4 border-b font-medium flex items-center justify-between">
            <span>Accepted — not yet in an issue</span>
            <span class="text-xs text-muted-foreground">{{ count($unassigned) }} result(s)</span>
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
    </div>

    <div class="card" wire:loading.class="opacity-50">
        <div class="p-4 border-b font-medium flex items-center justify-between">
            <span>In a Draft Issue</span>
            <span class="text-xs text-muted-foreground">{{ count($inDraftIssues) }} result(s)</span>
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
