<div x-data="{ open: false, url: '', label: '' }"
     x-on:open-pdf-modal.window="open = true; url = $event.detail.url; label = $event.detail.label ?? 'PDF Viewer'"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div @click.outside="open = false" class="bg-card rounded-lg shadow-xl w-full max-w-4xl h-[85vh] flex flex-col">
        <div class="flex items-center justify-between p-4 border-b">
            <p class="font-medium text-sm truncate" x-text="label"></p>
            <div class="flex items-center gap-3 shrink-0">
                <a :href="url" target="_blank" class="text-xs text-primary hover:underline">Open in new tab</a>
                <button type="button" @click="open = false" class="text-muted-foreground hover:text-foreground text-lg leading-none" aria-label="Close">&times;</button>
            </div>
        </div>
        <div class="flex-1 p-2">
            <embed :src="url" type="application/pdf" class="w-full h-full rounded">
        </div>
    </div>
</div>
