<div class="space-y-6">
    <h1 class="text-xl font-semibold">Audit Log</h1>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-muted-foreground">
                    <th class="p-3 font-medium">Time</th>
                    <th class="p-3 font-medium">User</th>
                    <th class="p-3 font-medium">Entity</th>
                    <th class="p-3 font-medium">Action</th>
                    <th class="p-3 font-medium">Changes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                    <tr class="border-b last:border-0 align-top">
                        <td class="p-3 whitespace-nowrap text-muted-foreground">{{ !empty($entry['created_at']) ? \Illuminate\Support\Carbon::parse($entry['created_at'])->format('Y-m-d H:i') : '' }}</td>
                        <td class="p-3">{{ $entry['user_id'] ?? '—' }}</td>
                        <td class="p-3">{{ $entry['entity_type'] }} #{{ $entry['entity_id'] }}</td>
                        <td class="p-3"><span class="badge">{{ $entry['action'] }}</span></td>
                        <td class="p-3 text-xs text-muted-foreground max-w-xs truncate" title="{{ $entry['new_value'] ?? '' }}">
                            {{ $entry['new_value'] ?? '' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-3 text-muted-foreground">No audit entries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
