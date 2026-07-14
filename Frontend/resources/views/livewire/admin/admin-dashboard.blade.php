<div class="space-y-6">
    <h1 class="text-xl font-semibold">Admin Dashboard</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Users</p>
            <p class="text-2xl font-semibold">{{ $totalUsers }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Journals</p>
            <p class="text-2xl font-semibold">{{ $totalJournals }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Manuscripts</p>
            <p class="text-2xl font-semibold">{{ $totalManuscripts }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-muted-foreground uppercase">Articles</p>
            <p class="text-2xl font-semibold">{{ $totalArticles }}</p>
        </div>
    </div>

    <div class="card p-6 flex gap-4 text-sm">
        <a href="{{ route('admin.users') }}" class="text-primary hover:underline">Manage Users</a>
        <a href="{{ route('admin.roles') }}" class="text-primary hover:underline">Manage Roles</a>
        <a href="{{ route('admin.journals') }}" class="text-primary hover:underline">Manage Journals</a>
        <a href="{{ route('admin.manuscripts') }}" class="text-primary hover:underline">Manage Manuscripts</a>
        <a href="{{ route('admin.audit') }}" class="text-primary hover:underline">Audit Log</a>
    </div>
</div>
