<?php

namespace App\Livewire\Admin;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Journal Management')]
class JournalManagement extends Component
{
    public array $journals = [];
    public int $perPage = 25;
    public int $page = 1;
    public array $editors = [];

    public bool $showCreate = false;
    public string $new_title = '';
    public string $new_issn = '';
    public string $new_scope_description = '';
    public string $new_editor_in_chief_id = '';
    public string $createError = '';

    public ?int $editingJournalId = null;
    public string $edit_title = '';
    public string $edit_issn = '';
    public string $edit_scope_description = '';
    public string $edit_editor_in_chief_id = '';
    public string $editError = '';

    public function mount(BackendClient $backend)
    {
        $this->load($backend);

        $response = $backend->get('/users', ['role' => 'Editor', 'per_page' => $this->perPage, 'page' => $this->page]);
        $this->editors = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get('/journals', ['include_archived' => 1, 'per_page' => $this->perPage, 'page' => $this->page]);
        $this->journals = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function create(BackendClient $backend)
    {
        $this->createError = '';

        $this->validate([
            'new_title' => 'required|string',
            'new_issn' => 'nullable|string',
            'new_scope_description' => 'nullable|string',
        ]);

        $response = $backend->post('/journals', [
            'title' => $this->new_title,
            'issn' => $this->new_issn ?: null,
            'scope_description' => $this->new_scope_description ?: null,
            'editor_in_chief_id' => $this->new_editor_in_chief_id !== '' ? (int) $this->new_editor_in_chief_id : null,
        ]);

        if (! $response->successful()) {
            $this->createError = $response->json('message') ?? 'Could not create journal.';

            return;
        }

        $this->reset(['new_title', 'new_issn', 'new_scope_description', 'new_editor_in_chief_id', 'showCreate']);
        $this->load($backend);
    }

    public function archive(int $id, BackendClient $backend)
    {
        $backend->post("/journals/{$id}/archive");
        $this->load($backend);
    }

    public function restore(int $id, BackendClient $backend)
    {
        $backend->post("/journals/{$id}/restore");
        $this->load($backend);
    }

    public function startEdit(int $journalId)
    {
        $journal = collect($this->journals)->firstWhere('id', $journalId);
        $this->editingJournalId = $journalId;
        $this->edit_title = $journal['title'] ?? '';
        $this->edit_issn = $journal['issn'] ?? '';
        $this->edit_scope_description = $journal['scope_description'] ?? '';
        $this->edit_editor_in_chief_id = (string) ($journal['editor_in_chief_id'] ?? '');
        $this->editError = '';
    }

    public function saveEdit(BackendClient $backend)
    {
        $this->editError = '';

        $response = $backend->patch("/journals/{$this->editingJournalId}", [
            'title' => $this->edit_title,
            'issn' => $this->edit_issn ?: null,
            'scope_description' => $this->edit_scope_description ?: null,
            'editor_in_chief_id' => $this->edit_editor_in_chief_id !== '' ? (int) $this->edit_editor_in_chief_id : null,
        ]);

        if (! $response->successful()) {
            $this->editError = $response->json('message') ?? 'Could not update journal.';

            return;
        }

        $this->editingJournalId = null;
        $this->load($backend);
    }

    public function previousPage(BackendClient $backend): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->load($backend);
        }
    }

    public function nextPage(BackendClient $backend): void
    {
        if (count($this->journals) === $this->perPage) {
            $this->page++;
            $this->load($backend);
        }
    }

    public function goToPage(int $page): void
    {
        $this->page = $page;
        $backend = app(BackendClient::class);
        $this->load($backend);
    }

    public function render()
    {
        return view('livewire.admin.journal-management');
    }
}
