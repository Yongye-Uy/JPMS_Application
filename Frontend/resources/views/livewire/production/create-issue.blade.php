<div class="max-w-lg space-y-6">
    <h1 class="text-xl font-semibold">Create Issue</h1>

    @if ($error)
        <div class="alert-error">{{ $error }}</div>
    @endif

    <form wire:submit="submit" class="card p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Journal</label>
            <select wire:model="journal_id" class="field w-full">
                <option value="">Select…</option>
                @foreach ($journals as $j)
                    <option value="{{ $j['id'] }}">{{ $j['title'] }}</option>
                @endforeach
            </select>
            @error('journal_id') <p class="text-sm text-destructive mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-3 gap-2">
            <div>
                <label class="block text-sm font-medium mb-1">Volume</label>
                <input type="number" wire:model="volume" class="field w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Number</label>
                <input type="number" wire:model="number" class="field w-full">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Year</label>
                <input type="number" wire:model="year" class="field w-full">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Publication Date</label>
            <input type="date" wire:model="publication_date" class="field w-full">
        </div>
        <button type="submit" class="btn-primary w-full">Create Issue</button>
    </form>
</div>
