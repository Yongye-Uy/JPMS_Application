<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Manuscript Management</h1>
    </div>

    <div class="card p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium mb-1">Search title</label>
            <input type="text" wire:model.live.debounce.400ms="search" class="field text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Status</label>
            <select wire:model.live="status" class="field text-sm">
                <option value="">All</option>
                <option value="Draft">Draft</option>
                <option value="Submitted">Submitted</option>
                <option value="Under Review">Under Review</option>
                <option value="Ready for Decision">Ready for Decision</option>
                <option value="Revision Required">Revision Required</option>
                <option value="Accepted">Accepted</option>
                <option value="Rejected">Rejected</option>
                <option value="Withdrawn">Withdrawn</option>
                <option value="Published">Published</option>
                <option value="Archived">Archived</option>
            </select>
        </div>
    </div>

    @if ($actionError)
        <div class="alert-error">{{ $actionError }}</div>
    @endif

    <div class="card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-muted-foreground">
                    <th class="p-3 font-medium">Title</th>
                    <th class="p-3 font-medium">Journal</th>
                    <th class="p-3 font-medium">Author</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($manuscripts as $m)
                    <tr class="border-b last:border-0">
                        <td class="p-3">
                            <a href="{{ route('admin.manuscripts.show', ['submissionId' => $m['id']]) }}" class="hover:underline">{{ $m['title'] }}</a>
                        </td>
                        <td class="p-3 text-muted-foreground">{{ $m['journal']['title'] ?? '—' }}</td>
                        <td class="p-3 text-muted-foreground">{{ $m['author']['full_name'] ?? '—' }}</td>
                        <td class="p-3">
                            <span class="rounded px-2 py-0.5 text-xs {{ $m['status'] === 'Archived' ? 'bg-red-100 text-red-700' : 'bg-muted' }}">
                                {{ $m['status'] }}
                            </span>
                        </td>
                        <td class="p-3 text-right space-x-2 whitespace-nowrap">
                            @if ($m['status'] === 'Archived')
                                <button wire:click="restore({{ $m['id'] }})" wire:confirm="Restore this manuscript to {{ $m['pre_archive_status'] ?? 'Submitted' }}?" class="text-primary hover:underline">Restore</button>
                            @else
                                <button wire:click="archive({{ $m['id'] }})" wire:confirm="Archive this manuscript?{{ $m['status'] === 'Published' ? ' This will also take it down from public view.' : '' }}" class="text-destructive hover:underline">Archive</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-3 text-muted-foreground">No manuscripts match.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @php $lastPage = $page + (count($manuscripts) < $perPage ? 0 : 1); @endphp
    <div class="flex items-center justify-between my-4">
        <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
            @disabled($page <= 1) class="btn-outline btn-sm">Previous</button>
        <div class="flex items-center gap-1">
            @foreach (range(max(1, $page - 2), min($lastPage, $page + 2)) as $p)
                <button type="button" wire:click="gotoPage({{ $p }})" wire:loading.attr="disabled"
                    @disabled($p === $page)
                    class="btn-sm {{ $p === $page ? 'btn-primary' : 'btn-outline' }}">{{ $p }}</button>
            @endforeach
        </div>
        <button type="button" wire:click="nextPage" wire:loading.attr="disabled"
            @disabled($page >= $lastPage) class="btn-outline btn-sm">Next</button>
    </div>
</div>
