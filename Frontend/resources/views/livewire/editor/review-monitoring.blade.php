<div class="max-w-3xl space-y-6">
    @if ($notFound)
        <div class="card p-6 text-muted-foreground">Manuscript not found.</div>
    @else
        <h1 class="text-xl font-semibold">Review Monitoring — {{ $manuscript['title'] }}</h1>

        <div class="card p-6">
            <p class="text-sm font-medium mb-3">Invitations</p>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-muted-foreground">
                        <th class="p-2 font-medium">Reviewer</th>
                        <th class="p-2 font-medium">Status</th>
                        <th class="p-2 font-medium">Deadline</th>
                        <th class="p-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($manuscript['review_invitations'] ?? [] as $inv)
                        <tr class="border-b last:border-0">
                            <td class="p-2">{{ $inv['reviewer']['full_name'] ?? '' }}</td>
                            <td class="p-2">
                                <span class="badge">{{ $inv['status'] }}</span>
                                @if ($inv['status'] === 'Declined' && !empty($inv['declined_reason']))
                                    <p class="text-xs text-muted-foreground mt-1">Reason: {{ $inv['declined_reason'] }}</p>
                                @endif
                            </td>
                            <td class="p-2 text-muted-foreground">{{ !empty($inv['deadline']) ? \Illuminate\Support\Carbon::parse($inv['deadline'])->toFormattedDateString() : '' }}</td>
                            <td class="p-2 text-right">
                                @if (!empty($inv['review']['id']))
                                    <a href="{{ route('editor.review-detail', ['reviewId' => $inv['review']['id']]) }}" class="text-primary hover:underline">View Review</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-2 text-muted-foreground">No invitations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card p-6">
            <p class="text-sm font-medium mb-3">Assign Reviewer</p>
            @if ($inviteMessage)
                <div class="mb-3 alert-success">{{ $inviteMessage }}</div>
            @endif
            @if ($inviteError)
                <div class="mb-3 alert-error">{{ $inviteError }}</div>
            @endif
            @if ($selectedReviewerId)
                <div class="flex items-center justify-between rounded border p-2 mb-2 text-sm">
                    <span>Selected reviewer: <strong>{{ $selectedReviewerName }}</strong></span>
                    <button type="button" wire:click="$set('selectedReviewerId', null)" class="text-xs text-muted-foreground underline">Change</button>
                </div>
            @else
                <div x-data="{ open: false }" class="relative mb-2" @click.outside="open = false">
                    <div class="flex gap-2">
                        <input type="text" wire:model="reviewer_search" x-on:focus="open = true" placeholder="Search reviewer by name/email" class="field text-sm flex-1">
                        <button type="button" wire:click="searchReviewers" x-on:click="open = true" class="btn-outline btn-sm">Search</button>
                    </div>
                    @if (!empty($reviewer_results))
                        <ul x-show="open" x-cloak class="absolute z-10 mt-1 w-full bg-card border rounded shadow-lg divide-y text-sm">
                            @foreach ($reviewer_results as $r)
                                <li class="py-2 px-3 flex items-center justify-between">
                                    <span>{{ $r['full_name'] }} ({{ $r['email'] }})</span>
                                    <button type="button" wire:click="pickReviewer({{ $r['id'] }}, '{{ addslashes($r['full_name']) }}')" class="text-xs btn-primary btn-sm">Select</button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
            <div class="flex items-end gap-2">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1">Review deadline</label>
                    <input type="date" wire:model="deadline" class="field text-sm">
                </div>
                <button wire:click="inviteReviewer" class="btn-primary btn-sm" @if(!$selectedReviewerId) disabled @endif>Invite</button>
            </div>
            @error('reviewer_id') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            @error('deadline') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
        </div>
    @endif
</div>
