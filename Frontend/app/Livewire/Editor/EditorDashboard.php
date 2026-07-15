<?php

namespace App\Livewire\Editor;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Editor Dashboard')]
class EditorDashboard extends Component
{
    public string $search = '';
    public string $status = '';
    public array $manuscripts = [];
    public int $page = 1;
    public int $lastPage = 1;
    public int $total = 0;

    public function mount(BackendClient $backend)
    {
        $this->load($backend);
    }

    public function updatedSearch(BackendClient $backend)
    {
        $this->page = 1;
        $this->load($backend);
    }

    public function updatedStatus(BackendClient $backend)
    {
        $this->page = 1;
        $this->load($backend);
    }

    public function nextPage(BackendClient $backend)
    {
        if ($this->page < $this->lastPage) {
            $this->page++;
            $this->load($backend);
        }
    }

    public function prevPage(BackendClient $backend)
    {
        if ($this->page > 1) {
            $this->page--;
            $this->load($backend);
        }
    }

    public function gotoPage(BackendClient $backend, int $p)
    {
        $this->page = $p;
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $query = array_filter([
            'q'        => $this->search,
            'status'   => $this->status,
            'per_page' => 25,
            'page'     => $this->page,
        ]);

        $response = $backend->get('/manuscripts', $query);
        $json = $response->successful() ? $response->json() : [];

        $all = $json['data'] ?? [];

        // When no status filter is active, hide Draft & Withdrawn
        if ($this->status === '') {
            $all = array_values(array_filter($all, fn ($m) => ! in_array($m['status'], ['Draft', 'Withdrawn'])));
        }

        $this->manuscripts = $all;
        $this->lastPage    = $json['last_page'] ?? 1;
        $this->total       = $json['total'] ?? count($all);
    }

    public function render()
    {
        return view('livewire.editor.editor-dashboard');
    }
}
