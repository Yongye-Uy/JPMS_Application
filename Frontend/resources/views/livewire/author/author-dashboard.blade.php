<div class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Author Dashboard</h1>
        <a href="{{ route('author.submissions.create') }}" class="btn-primary btn-sm">New Submission</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach ($counts as $status => $count)
            <div class="card p-4 text-center">
                <p class="text-2xl font-semibold">{{ $count }}</p>
                <p class="text-xs text-muted-foreground mt-1">{{ $status }}</p>
            </div>
        @endforeach
    </div>

    <div class="card p-6">
        <h2 class="font-medium mb-4">Recent submissions</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted-foreground border-b">
                    <th class="pb-2">Title</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Updated</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($recent as $m)
                    <tr>
                        <td class="py-2">
                            <a href="{{ route('author.submissions.show', ['submissionId' => $m['id']]) }}" class="hover:underline">{{ $m['title'] }}</a>
                        </td>
                        <td class="py-2">{{ $m['status'] }}</td>
                        <td class="py-2 text-muted-foreground">{{ \Illuminate\Support\Carbon::parse($m['updated_at'])->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-muted-foreground">No submissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
