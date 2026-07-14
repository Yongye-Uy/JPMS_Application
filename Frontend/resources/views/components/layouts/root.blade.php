<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'JPMS' }} — Journal Publication Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-background text-foreground">
    <header class="border-b border-border bg-card">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="font-semibold text-lg text-foreground">JPMS</a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('public.home') }}" class="text-foreground/80 hover:text-foreground">Search</a>
                <a href="{{ route('public.browse-issues') }}" class="text-foreground/80 hover:text-foreground">Browse Issues</a>
                @if (\App\Support\AuthenticatedUser::check())
                    <a href="{{ route('dashboard.home') }}" class="text-foreground/80 hover:text-foreground">Dashboard</a>
                    <a href="{{ route('profile') }}" class="text-foreground/80 hover:text-foreground">{{ \App\Support\AuthenticatedUser::fullName() }}</a>
                @else
                    <a href="{{ route('auth.login') }}" class="text-foreground/80 hover:text-foreground">Login</a>
                    <a href="{{ route('auth.register') }}" class="btn-primary btn-sm">Register</a>
                @endif
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="border-t border-border mt-16 py-6 text-center text-sm text-muted-foreground">
        Journal Publication Management System — free & open access
    </footer>

    <x-pdf-viewer-modal />
    @livewireScripts
</body>
</html>
