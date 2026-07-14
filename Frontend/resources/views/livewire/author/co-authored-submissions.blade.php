<div class="space-y-6">
    <h1 class="text-xl font-semibold">Co-Authored Submissions</h1>

    <div class="card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-muted-foreground">
                    <th class="p-3 font-medium">Title</th>
                    <th class="p-3 font-medium">Journal</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($manuscripts as $m)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $m['title'] }}</td>
                        <td class="p-3 text-muted-foreground">{{ $m['journal']['title'] ?? '' }}</td>
                        <td class="p-3"><span class="badge">{{ $m['status'] }}</span></td>
                        <td class="p-3 text-right">
                            <a href="{{ route('author.submissions.show', ['submissionId' => $m['id']]) }}" class="text-primary hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-3 text-muted-foreground">No co-authored submissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
