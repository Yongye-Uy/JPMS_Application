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
    public int $page = 1;
    public int $lastPage = 1;
    public int $total = 0;

    public function mount(BackendClient $backend): void
    {
        $this->load($backend);
    }

    public function updatedSearch(BackendClient $backend): void
    {
        $this->page = 1;
        $this->load($backend);
    }

    public function updatedStatusFilter(BackendClient $backend): void
    {
        $this->page = 1;
        $this->load($backend);
    }

    public function nextPage(BackendClient $backend): void
    {
        if ($this->page < $this->lastPage) {
            $this->page++;
            $this->load($backend);
        }
    }

    public function prevPage(BackendClient $backend): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->load($backend);
        }
    }

    public function gotoPage(BackendClient $backend, int $p): void
    {
        $this->page = $p;
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $query = ['per_page' => 25, 'page' => $this->page];

        if ($this->search !== '') {
            $query['q'] = $this->search;
        }
        if ($this->statusFilter !== '') {
            $query['status'] = $this->statusFilter;
        }

        $response = $backend->get('/issues', $query);
        $json = $response->successful() ? $response->json() : [];

        $this->issues    = $json['data'] ?? [];
        $this->lastPage  = $json['last_page'] ?? 1;
        $this->total     = $json['total'] ?? count($this->issues);
    }

    public function render()
    {
        return view('livewire.production.issue-management');
    }
}
