<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — JPMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-background text-foreground">
    @php
        $roles = \App\Support\AuthenticatedUser::roleNames();
        $fullName = \App\Support\AuthenticatedUser::fullName();
        $email = \App\Support\AuthenticatedUser::email();
        $initials = collect(preg_split('/\s+/', trim($fullName ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->join('') ?: '?';

        $navGroupLabel = 'px-2 text-xs font-semibold text-sidebar-foreground/50 uppercase tracking-wide mb-1';
        $navBase = 'block rounded-md px-2 py-1.5 text-sm transition-colors';
        $navLink = "{$navBase} text-sidebar-foreground/80 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground";
        $navClass = fn (string ...$patterns) => request()->routeIs(...$patterns)
            ? "{$navBase} bg-primary/10 text-primary font-medium"
            : $navLink;
    @endphp
    <div class="flex min-h-screen">
        <aside class="w-64 shrink-0 sticky top-0 h-screen bg-sidebar border-r border-sidebar-border flex flex-col">
            <div class="p-6 border-b border-sidebar-border">
                <a href="{{ route('public.home') }}" class="font-semibold text-lg block text-sidebar-foreground">JPMS</a>
                <p class="text-sm text-sidebar-foreground/60 mt-1">Journal Publication Management</p>
            </div>

            <nav class="flex-1 min-h-0 overflow-y-auto p-4 space-y-6">
                @if (in_array('Reader', $roles))
                    <div>
                        <p class="{{ $navGroupLabel }}">Reader</p>
                        <a href="{{ route('reader.dashboard') }}" class="{{ $navClass('reader.dashboard') }}">My Account</a>
                    </div>
                @endif

                @if (in_array('Author', $roles))
                    <div>
                        <p class="{{ $navGroupLabel }}">Author</p>
                        <a href="{{ route('author.dashboard') }}" class="{{ $navClass('author.dashboard') }}">Dashboard</a>
                        <a href="{{ route('author.submissions') }}" class="{{ $navClass('author.submissions', 'author.submissions.show') }}">My Submissions</a>
                        <a href="{{ route('author.submissions.create') }}" class="{{ $navClass('author.submissions.create') }}">New Submission</a>
                        <a href="{{ route('author.co-author-invitations') }}" class="{{ $navClass('author.co-author-invitations') }}">Co-Author Invitations</a>
                        <a href="{{ route('author.co-authored-submissions') }}" class="{{ $navClass('author.co-authored-submissions') }}">Co-Authored Papers</a>
                        <a href="{{ route('author.metrics') }}" class="{{ $navClass('author.metrics') }}">My Metrics</a>
                    </div>
                @endif

                @if (in_array('Reviewer', $roles))
                    <div>
                        <p class="{{ $navGroupLabel }}">Reviewer</p>
                        <a href="{{ route('reviewer.dashboard') }}" class="{{ $navClass('reviewer.dashboard', 'reviewer.invitations.show', 'reviewer.reviews.submit') }}">Dashboard</a>
                        <a href="{{ route('reviewer.history') }}" class="{{ $navClass('reviewer.history', 'reviewer.history.show') }}">Review History</a>
                    </div>
                @endif

                @if (in_array('Editor', $roles))
                    <div>
                        <p class="{{ $navGroupLabel }}">Editor</p>
                        <a href="{{ route('editor.dashboard') }}" class="{{ $navClass('editor.dashboard', 'editor.submissions.show', 'editor.reviews.monitor', 'editor.review-detail', 'editor.decision') }}">Editorial Dashboard</a>
                        <a href="{{ route('editor.reviewers.search') }}" class="{{ $navClass('editor.reviewers.search') }}">Reviewer Search</a>
                        <a href="{{ route('production.dashboard') }}" class="{{ $navClass('production.*') }}">Production</a>
                        <a href="{{ route('analytics.dashboard') }}" class="{{ $navClass('analytics.dashboard') }}">Analytics</a>
                    </div>
                @endif

                @if (in_array('Admin', $roles))
                    <div>
                        <p class="{{ $navGroupLabel }}">Admin</p>
                        <a href="{{ route('admin.dashboard') }}" class="{{ $navClass('admin.dashboard') }}">Admin Dashboard</a>
                        <a href="{{ route('admin.users') }}" class="{{ $navClass('admin.users') }}">User Management</a>
                        <a href="{{ route('admin.roles') }}" class="{{ $navClass('admin.roles') }}">Role Management</a>
                        <a href="{{ route('admin.journals') }}" class="{{ $navClass('admin.journals') }}">Journals</a>
                        <a href="{{ route('admin.manuscripts') }}" class="{{ $navClass('admin.manuscripts', 'admin.manuscripts.show') }}">Manuscripts</a>
                        <a href="{{ route('admin.audit') }}" class="{{ $navClass('admin.audit') }}">Audit Log</a>
                    </div>
                @endif

                <div class="pt-4 border-t border-sidebar-border">
                    <a href="{{ route('public.home') }}" class="{{ $navLink }}">Public Search</a>
                </div>
            </nav>

            <div class="p-4 border-t border-sidebar-border">
                <div class="flex items-center gap-3 mb-3">
                    <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="text-sm font-medium text-primary">{{ $initials }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-sidebar-foreground truncate">{{ $fullName }}</p>
                        <p class="text-xs text-sidebar-foreground/60 truncate">{{ $email }}</p>
                    </div>
                </div>
                <a href="{{ route('profile') }}" class="{{ $navLink }} text-center">Profile</a>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="w-full {{ $navBase }} text-center text-destructive hover:bg-destructive/10">Log out</button>
                </form>
            </div>
        </aside>

        <div class="flex-1">
            <main class="max-w-5xl mx-auto px-6 py-8">
                {{ $slot }}
            </main>
        </div>
    </div>
    <x-pdf-viewer-modal />
    @livewireScripts
</body>
</html>
