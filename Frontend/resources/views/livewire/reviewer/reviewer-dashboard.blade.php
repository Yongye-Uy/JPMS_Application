<div class="space-y-8">
    <h1 class="text-xl font-semibold">Reviewer Dashboard</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Pending Invitations</p>
            <p class="text-2xl font-semibold">{{ count($this->pending()) }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">In Progress</p>
            <p class="text-2xl font-semibold">{{ count($this->active()) }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Completed</p>
            <p class="text-2xl font-semibold">{{ count($this->completed()) }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Overdue</p>
            <p class="text-2xl font-semibold text-destructive">{{ count($this->overdue()) }}</p>
        </div>
    </div>

    <div class="card">
        <div class="p-4 border-b font-medium">Pending Invitations</div>
        <table class="w-full text-sm">
            <tbody>
                @forelse ($this->pending() as $inv)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $inv['manuscript']['title'] ?? '—' }}</td>
                        <td class="p-3 text-muted-foreground">Deadline: {{ \Illuminate\Support\Carbon::parse($inv['deadline'])->toFormattedDateString() }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('reviewer.invitations.show', ['invitationId' => $inv['id']]) }}" class="text-primary hover:underline">Respond</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-muted-foreground">No pending invitations.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="p-4 border-b font-medium">Active Reviews</div>
        <table class="w-full text-sm">
            <tbody>
                @forelse ($this->active() as $inv)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $inv['manuscript']['title'] ?? '—' }}</td>
                        <td class="p-3 text-muted-foreground">Deadline: {{ \Illuminate\Support\Carbon::parse($inv['deadline'])->toFormattedDateString() }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('reviewer.reviews.submit', ['invitationId' => $inv['id']]) }}" class="text-primary hover:underline">Continue Review</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-muted-foreground">No active reviews.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
