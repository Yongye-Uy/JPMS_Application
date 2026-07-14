<?php

namespace App\Livewire\Public;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.root')]
#[Title('Browse Issues')]
class BrowseIssues extends Component
{
    public array $journals = [];

    public function mount(BackendClient $backend)
    {
        $response = $backend->get('/issues', ['per_page' => 100]);
        $issues = $response->successful() ? ($response->json('data') ?? []) : [];

        $byJournal = [];
        foreach ($issues as $issue) {
            $journal = $issue['journal'] ?? ['id' => 0, 'title' => 'Unknown Journal'];
            $byJournal[$journal['id']]['journal'] = $journal;
            $byJournal[$journal['id']]['issues'][] = $issue;
        }

        $this->journals = array_values($byJournal);
    }

    public function render()
    {
        return view('livewire.public.browse-issues');
    }
}
