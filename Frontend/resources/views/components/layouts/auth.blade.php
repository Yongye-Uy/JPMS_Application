<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'JPMS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-background text-foreground min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <a href="{{ route('public.home') }}" class="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground mb-4">
            &larr; Back to Home
        </a>
        <div class="text-center mb-6">
            <a href="{{ route('public.home') }}" class="font-semibold text-2xl text-foreground">JPMS</a>
            <p class="text-sm text-muted-foreground">Journal Publication Management System</p>
        </div>
        <div class="card p-6">
            {{ $slot }}
        </div>
    </div>
    @livewireScripts
</body>
</html>
