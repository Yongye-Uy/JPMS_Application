<div class="space-y-6">
    <h1 class="text-xl font-semibold">Review History</h1>

    <div class="card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-muted-foreground">
                    <th class="p-3 font-medium">Manuscript</th>
                    <th class="p-3 font-medium">Journal</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Submitted</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invitations as $inv)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $inv['manuscript']['title'] ?? '—' }}</td>
                        <td class="p-3 text-muted-foreground">{{ $inv['manuscript']['journal']['title'] ?? '—' }}</td>
                        <td class="p-3">
                            <span class="badge">{{ $inv['status'] }}</span>
                            @if ($inv['status'] === 'Declined' && !empty($inv['declined_reason']))
                                <p class="text-xs text-muted-foreground mt-1">Reason: {{ $inv['declined_reason'] }}</p>
                            @endif
                        </td>
                        <td class="p-3 text-muted-foreground">{{ !empty($inv['review']['submitted_at']) ? \Illuminate\Support\Carbon::parse($inv['review']['submitted_at'])->toFormattedDateString() : '—' }}</td>
                        <td class="p-3 text-right">
                            @if (!empty($inv['review']['id']))
                                <a href="{{ route('reviewer.history.show', ['reviewId' => $inv['review']['id']]) }}" class="text-primary hover:underline">View</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-3 text-muted-foreground">No review invitations yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
