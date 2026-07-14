<div class="max-w-3xl space-y-6">
    @if ($notFound)
        <div class="card p-6 text-muted-foreground">Issue not found.</div>
    @else
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">
                {{ $issue['journal']['title'] ?? '' }} — Vol {{ $issue['volume'] }}, No {{ $issue['number'] }} ({{ $issue['year'] }})
            </h1>
            <span class="badge">{{ $issue['status'] }}</span>
        </div>

        @if ($publishError)
            <div class="alert-error">{{ $publishError }}</div>
        @endif

        <div class="card">
            <div class="p-4 border-b font-medium flex items-center justify-between">
                Articles
                @if ($issue['status'] === 'Draft')
                    <button wire:click="publish" class="btn-primary btn-sm"
                        @disabled(empty($issue['articles']))>
                        Publish Issue
                    </button>
                @endif
            </div>
            <table class="w-full text-sm">
                <tbody>
                    @forelse ($issue['articles'] ?? [] as $article)
                        <tr class="border-b last:border-0">
                            <td class="p-3">{{ $article['manuscript']['title'] ?? '' }}</td>
                            <td class="p-3 text-muted-foreground">pp. {{ $article['page_start'] }}–{{ $article['page_end'] }}</td>
                            <td class="p-3 text-muted-foreground">{{ $article['doi'] }}</td>
                            <td class="p-3 text-right">
                                @if ($issue['status'] === 'Draft')
                                    <button wire:click="removeArticle({{ $article['id'] }})" wire:confirm="Remove this article?" class="text-destructive hover:underline">Remove</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-3 text-muted-foreground">No articles yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($issue['status'] === 'Draft')
            <div class="card p-6">
                <p class="text-sm font-medium mb-3">Add Article</p>
                @if ($addMessage)
                    <div class="mb-3 alert-success">{{ $addMessage }}</div>
                @endif
                @if ($addError)
                    <div class="mb-3 alert-error">{{ $addError }}</div>
                @endif
                <div class="grid grid-cols-3 gap-2">
                    <select wire:model="manuscript_id" class="field text-sm col-span-1">
                        <option value="">Accepted manuscript…</option>
                        @foreach ($availableManuscripts as $m)
                            <option value="{{ $m['id'] }}">{{ $m['title'] }}</option>
                        @endforeach
                    </select>
                    <input type="number" wire:model="page_start" placeholder="Start page" class="field text-sm">
                    <input type="number" wire:model="page_end" placeholder="End page" class="field text-sm">
                </div>
                <button wire:click="addArticle" class="mt-3 btn-primary btn-sm">Add</button>
                @error('manuscript_id') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
            </div>
        @endif
    @endif
</div>
