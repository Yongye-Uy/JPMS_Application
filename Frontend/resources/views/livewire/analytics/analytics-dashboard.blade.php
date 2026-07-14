<div class="space-y-8">
    <h1 class="text-xl font-semibold">Analytics & Reporting</h1>

    <div class="card p-6">
        <h2 class="font-medium mb-4">Journal Performance</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><p class="text-muted-foreground">Total Submissions</p><p class="text-xl font-semibold">{{ $journalReport['total_submissions'] ?? '—' }}</p></div>
            <div><p class="text-muted-foreground">Accepted</p><p class="text-xl font-semibold">{{ $journalReport['accepted'] ?? '—' }}</p></div>
            <div><p class="text-muted-foreground">Acceptance Rate</p><p class="text-xl font-semibold">{{ $journalReport['acceptance_rate'] ?? '—' }}%</p></div>
            <div><p class="text-muted-foreground">Avg Review Days</p><p class="text-xl font-semibold">{{ $journalReport['avg_review_days'] ?? '—' }}</p></div>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="font-medium mb-4">Reviewer Performance</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><p class="text-muted-foreground">Total Invitations</p><p class="text-xl font-semibold">{{ $reviewerReport['total_invitations'] ?? '—' }}</p></div>
            <div><p class="text-muted-foreground">Completed</p><p class="text-xl font-semibold">{{ $reviewerReport['completed'] ?? '—' }}</p></div>
            <div><p class="text-muted-foreground">Completion Rate</p><p class="text-xl font-semibold">{{ $reviewerReport['completion_rate'] ?? '—' }}%</p></div>
            <div><p class="text-muted-foreground">Avg Turnaround Days</p><p class="text-xl font-semibold">{{ $reviewerReport['avg_turnaround_days'] ?? '—' }}</p></div>
        </div>
    </div>

    <div class="card">
        <div class="p-4 border-b font-medium">Top Articles by Views</div>
        <table class="w-full text-sm">
            <tbody>
                @forelse ($topArticles as $article)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $article['manuscript']['title'] ?? '' }}</td>
                        <td class="p-3 text-muted-foreground">{{ $article['views'] ?? 0 }} views</td>
                        <td class="p-3 text-muted-foreground">{{ $article['downloads'] ?? 0 }} downloads</td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-muted-foreground">No published articles yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
