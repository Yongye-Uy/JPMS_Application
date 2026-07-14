<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">My Submissions</h1>
        <a href="{{ route('author.submissions.create') }}" class="btn-primary btn-sm">New Submission</a>
    </div>

    <div class="card p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium mb-1">Search title</label>
            <input type="text" wire:model.live.debounce.400ms="q" class="field text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Status</label>
            <select wire:model.live="status" class="field text-sm">
                <option value="">All</option>
                <option value="Draft">Draft</option>
                <option value="Submitted">Submitted</option>
                <option value="Under Review">Under Review</option>
                <option value="Revision Required">Revision Required</option>
                <option value="Accepted">Accepted</option>
                <option value="Rejected">Rejected</option>
                <option value="Withdrawn">Withdrawn</option>
                <option value="Published">Published</option>
            </select>
        </div>
    </div>

    <div class="card p-6">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted-foreground border-b">
                    <th class="pb-2">Title</th>
                    <th class="pb-2">Journal</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Updated</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($submissions as $m)
                    <tr>
                        <td class="py-2">
                            <a href="{{ route('author.submissions.show', ['submissionId' => $m['id']]) }}" class="hover:underline">{{ $m['title'] }}</a>
                        </td>
                        <td class="py-2 text-muted-foreground">{{ $m['journal']['title'] ?? '' }}</td>
                        <td class="py-2">{{ $m['status'] }}</td>
                        <td class="py-2 text-muted-foreground">{{ \Illuminate\Support\Carbon::parse($m['updated_at'])->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-muted-foreground">No submissions match.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
