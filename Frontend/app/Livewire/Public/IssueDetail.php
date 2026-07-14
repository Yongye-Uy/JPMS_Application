<?php

namespace App\Livewire\Public;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.root')]
#[Title('Issue')]
class IssueDetail extends Component
{
    public int $issueId;
    public array $issue = [];
    public bool $notFound = false;

    public function mount(int $issueId, BackendClient $backend)
    {
        $this->issueId = $issueId;

        $response = $backend->get("/issues/{$issueId}");
        if (! $response->successful()) {
            $this->notFound = true;

            return;
        }

        $this->issue = $response->json();
    }

    public function render()
    {
        return view('livewire.public.issue-detail');
    }
}
