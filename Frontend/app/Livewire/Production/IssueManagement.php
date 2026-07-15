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
    public string $search = '';
    public string $statusFilter = '';

    public function mount(BackendClient $backend): void
    {
        $this->load($backend);
    }

    public function updatedSearch(BackendClient $backend): void
    {
        $this->load($backend);
    }

    public function updatedStatusFilter(BackendClient $backend): void
    {
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $query = ['per_page' => 50];

        if ($this->search !== '') {
            $query['q'] = $this->search;
        }
        if ($this->statusFilter !== '') {
            $query['status'] = $this->statusFilter;
        }

        $response = $backend->get('/issues', $query);
        $this->issues = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function render()
    {
        return view('livewire.production.issue-management');
    }
}
