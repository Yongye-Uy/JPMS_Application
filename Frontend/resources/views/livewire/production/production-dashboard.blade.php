<div class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Production Dashboard</h1>
        <a href="{{ route('production.issues') }}" class="text-sm text-primary hover:underline">View All Issues</a>
    </div>

    <div class="card">
        <div class="p-4 border-b font-medium">Accepted — not yet in an issue</div>
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

    <div class="card">
        <div class="p-4 border-b font-medium">In a Draft Issue</div>
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
