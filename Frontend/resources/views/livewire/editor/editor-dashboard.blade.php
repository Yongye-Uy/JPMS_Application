<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Editor Dashboard</h1>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search title/abstract…" class="field text-sm">
    </div>

    <div class="card">
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
