<x-layouts.root :title="'Access Denied'">
    <div class="text-center py-16">
        <h1 class="text-2xl font-semibold mb-2">Access Denied</h1>
        <p class="text-muted-foreground mb-6">You don't have permission to view this page.</p>
        <div class="flex justify-center gap-3">
            <a href="{{ route('dashboard.home') }}" class="btn-primary">Go to Dashboard</a>
            <a href="{{ route('public.home') }}" class="btn-outline">Go Home</a>
        </div>
    </div>
</x-layouts.root>
