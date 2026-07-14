<?php

namespace App\Livewire\Author;

use App\Clients\BackendClient;
use App\Support\JournalOptions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('New Submission')]
class CreateSubmission extends Component
{
    public string $journal_id = '';
    public string $manuscript_type = 'Research Article';
    public string $title = '';
    public string $abstract = '';
    public string $keywords = '';
    public string $error = '';

    public array $journals = [];

    public bool $showNewJournal = false;
    public string $new_journal_title = '';
    public string $newJournalError = '';

    public function mount(BackendClient $backend)
    {
        $this->journals = JournalOptions::forSelect($backend);
        if (count($this->journals) === 1) {
            $this->journal_id = (string) $this->journals[0]['id'];
        }
    }

    public function createJournal(BackendClient $backend)
    {
        $this->newJournalError = '';

        $this->validate(['new_journal_title' => 'required|string'], [], ['new_journal_title' => 'journal title']);

        $response = $backend->post('/journals', ['title' => $this->new_journal_title]);

        if (! $response->successful()) {
            $this->newJournalError = $response->json('message') ?? 'Could not create journal.';

            return;
        }

        $journal = $response->json();
        $this->journals[] = ['id' => $journal['id'], 'title' => $journal['title']];
        $this->journal_id = (string) $journal['id'];
        $this->new_journal_title = '';
        $this->showNewJournal = false;
    }

    public function submit(BackendClient $backend)
    {
        $this->error = '';

        $this->validate([
            'journal_id' => 'required|integer',
            'manuscript_type' => 'required|string',
            'title' => 'required|string',
            'abstract' => 'nullable|string',
        ]);

        $keywords = collect(explode(',', $this->keywords))
            ->map(fn ($k) => trim($k))
            ->filter()
            ->values()
            ->all();

        $response = $backend->post('/manuscripts', [
            'journal_id' => (int) $this->journal_id,
            'manuscript_type' => $this->manuscript_type,
            'title' => $this->title,
            'abstract' => $this->abstract ?: null,
            'keywords' => $keywords,
        ]);

        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Could not create submission.';

            return;
        }

        return redirect()->route('author.submissions.show', ['submissionId' => $response->json('id')]);
    }

    public function render()
    {
        return view('livewire.author.create-submission');
    }
}
