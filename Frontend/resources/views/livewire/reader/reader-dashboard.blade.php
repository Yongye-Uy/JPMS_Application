<div class="space-y-6 max-w-lg">
    <h1 class="text-xl font-semibold">My Account</h1>

    <div class="card p-6">
        <p class="font-medium">{{ \App\Support\AuthenticatedUser::fullName() }}</p>
        <p class="text-sm text-muted-foreground">{{ \App\Support\AuthenticatedUser::email() }}</p>
        <p class="text-sm text-muted-foreground mt-1">Roles: {{ implode(', ', \App\Support\AuthenticatedUser::roleNames()) }}</p>
    </div>

    <div class="card p-6 space-y-2">
        <h2 class="font-medium mb-2">Quick links</h2>
        <a href="{{ route('public.home') }}" class="block text-sm hover:underline">Search Articles</a>
        <a href="{{ route('public.browse-issues') }}" class="block text-sm hover:underline">Browse Issues</a>
        <a href="{{ route('profile') }}" class="block text-sm hover:underline">Edit Profile</a>
        @if ($this->isAuthor())
            <a href="{{ route('author.submissions.create') }}" class="block text-sm hover:underline">Submit Research</a>
        @endif
    </div>
</div>
