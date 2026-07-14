<?php

namespace App\Livewire\Production;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Issue Management')]
class IssueManagement extends Component
{
    public array $issues = [];

    public function mount(BackendClient $backend)
    {
        $response = $backend->get('/issues', ['per_page' => 100]);
        $this->issues = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function render()
    {
        return view('livewire.production.issue-management');
    }
}
