<?php

namespace App\Livewire\Production;

use App\Clients\BackendClient;
use App\Support\JournalOptions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Create Issue')]
class CreateIssue extends Component
{
    public string $journal_id = '';
    public string $volume = '';
    public string $number = '';
    public string $year = '';
    public string $publication_date = '';
    public string $error = '';

    public array $journals = [];

    public function mount(BackendClient $backend)
    {
        $this->journals = JournalOptions::forSelect($backend);
        $this->year = (string) now()->year;
    }

    public function submit(BackendClient $backend)
    {
        $this->error = '';

        $this->validate([
            'journal_id' => 'required|integer',
            'volume' => 'required|integer',
            'number' => 'required|integer',
            'year' => 'required|integer',
            'publication_date' => 'nullable|date',
        ]);

        $response = $backend->post('/issues', [
            'journal_id' => (int) $this->journal_id,
            'volume' => (int) $this->volume,
            'number' => (int) $this->number,
            'year' => (int) $this->year,
            'publication_date' => $this->publication_date ?: null,
        ]);

        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Could not create issue.';

            return;
        }

        return redirect()->route('production.issues.manage', ['issueId' => $response->json('id')]);
    }

    public function render()
    {
        return view('livewire.production.create-issue');
    }
}
