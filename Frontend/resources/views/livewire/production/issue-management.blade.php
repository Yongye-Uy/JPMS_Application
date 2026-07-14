<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Issue Management</h1>
        <a href="{{ route('production.issues.create') }}" class="btn-primary btn-sm">Create New Issue</a>
    </div>

    <div class="card">
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
                    <tr><td colspan="6" class="p-3 text-muted-foreground">No issues yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
