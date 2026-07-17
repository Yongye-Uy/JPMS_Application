<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Journal Management</h1>
        <button wire:click="$toggle('showCreate')" class="btn-primary btn-sm">Add Journal</button>
    </div>

    @if ($showCreate)
        <div class="card p-6 space-y-3">
            @if ($createError)
                <div class="alert-error">{{ $createError }}</div>
            @endif
            <input type="text" wire:model="new_title" placeholder="Title" class="w-full field text-sm">
            <input type="text" wire:model="new_issn" placeholder="ISSN" class="w-full field text-sm">
            <textarea wire:model="new_scope_description" placeholder="Scope description" rows="2" class="w-full field text-sm"></textarea>
            <select wire:model="new_editor_in_chief_id" class="w-full field text-sm">
                <option value="">No editor-in-chief</option>
                @foreach ($editors as $e)
                    <option value="{{ $e['id'] }}">{{ $e['full_name'] }}</option>
                @endforeach
            </select>
            <button wire:click="create" class="btn-primary btn-sm">Create</button>
        </div>
    @endif

    <div class="card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-muted-foreground">
                    <th class="p-3 font-medium">Title</th>
                    <th class="p-3 font-medium">ISSN</th>
                    <th class="p-3 font-medium">Editor-in-Chief</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($journals as $journal)
                    <tr class="border-b last:border-0">
                        <td class="p-3">{{ $journal['title'] }}</td>
                        <td class="p-3 text-muted-foreground">{{ $journal['issn'] }}</td>
                        <td class="p-3 text-muted-foreground">{{ $journal['editor_in_chief']['full_name'] ?? '—' }}</td>
                        <td class="p-3">
                            <span class="rounded px-2 py-0.5 text-xs {{ $journal['is_archived'] ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $journal['is_archived'] ? 'Archived' : 'Active' }}
                            </span>
                        </td>
                        <td class="p-3 text-right space-x-2 whitespace-nowrap">
                            <button wire:click="startEdit({{ $journal['id'] }})" class="text-primary hover:underline">Edit</button>
                            @if ($journal['is_archived'])
                                <button wire:click="restore({{ $journal['id'] }})" class="text-primary hover:underline">Restore</button>
                            @else
                                <button wire:click="archive({{ $journal['id'] }})" class="text-destructive hover:underline">Archive</button>
                            @endif
                        </td>
                    </tr>
                    @if ($editingJournalId === $journal['id'])
                        <tr class="border-b last:border-0 bg-muted/30" wire:key="journal-edit-{{ $journal['id'] }}">
                            <td colspan="5" class="p-4 space-y-2">
                                @if ($editError)
                                    <div class="alert-error">{{ $editError }}</div>
                                @endif
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" wire:model="edit_title" placeholder="Title" class="field text-sm">
                                    <input type="text" wire:model="edit_issn" placeholder="ISSN" class="field text-sm">
                                    <select wire:model="edit_editor_in_chief_id" class="field text-sm">
                                        <option value="">No editor-in-chief</option>
                                        @foreach ($editors as $e)
                                            <option value="{{ $e['id'] }}">{{ $e['full_name'] }}</option>
                                        @endforeach
                                    </select>
                                    <textarea wire:model="edit_scope_description" placeholder="Scope description" rows="2" class="field text-sm col-span-2"></textarea>
                                </div>
                                <div class="flex gap-2">
                                    <button wire:click="saveEdit" class="btn-primary btn-sm">Save</button>
                                    <button wire:click="$set('editingJournalId', null)" class="btn-outline btn-sm">Cancel</button>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @php $lastPage = $page + (count($journals) < $perPage ? 0 : 1); @endphp
    <div class="flex items-center justify-between my-4">
        <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
            @disabled($page <= 1) class="btn-outline btn-sm">Previous</button>
        <div class="flex items-center gap-1">
            @foreach (range(max(1, $page - 2), min($lastPage, $page + 2)) as $p)
                <button type="button" wire:click="goToPage({{ $p }})" wire:loading.attr="disabled"
                    @disabled($p === $page)
                    class="btn-sm {{ $p === $page ? 'btn-primary' : 'btn-outline' }}">{{ $p }}</button>
            @endforeach
        </div>
        <button type="button" wire:click="nextPage" wire:loading.attr="disabled"
            @disabled($page >= $lastPage) class="btn-outline btn-sm">Next</button>
    </div>
    </div>
</div>
