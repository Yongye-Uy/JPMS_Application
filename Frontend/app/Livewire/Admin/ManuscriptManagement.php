<?php

namespace App\Livewire\Admin;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Manuscript Management')]
class ManuscriptManagement extends Component
{
    public array $manuscripts = [];
    public int $perPage = 25;
    public int $page = 1;
    public string $status = 'Published';
    public string $search = '';
    public string $actionError = '';
    public int $total = 0;

    public function mount(BackendClient $backend)
    {
        $this->load($backend);
    }

    public function updatedStatus(BackendClient $backend)
    {
        $this->load($backend);
    }

    public function updatedSearch(BackendClient $backend)
    {
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $params = array_filter([
            'status' => $this->status ?: null,
            'q' => $this->search ?: null,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ]);

        $response = $backend->get('/manuscripts', $params);
        if ($response->successful()) {
            $this->manuscripts = $response->json('data') ?? [];
            $this->total = $response->json('total') ?? count($this->manuscripts);
        } else {
            $this->manuscripts = [];
            $this->total = 0;
        }
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
        if (count($this->manuscripts) === $this->perPage) {
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



    public function archive(int $id, BackendClient $backend)
    {
        $this->actionError = '';
        $response = $backend->post("/manuscripts/{$id}/archive");
        if (! $response->successful()) {
            $this->actionError = $response->json('message') ?? 'Could not archive manuscript.';
        }
        $this->load($backend);
    }

    public function restore(int $id, BackendClient $backend)
    {
        $this->actionError = '';
        $response = $backend->post("/manuscripts/{$id}/restore");
        if (! $response->successful()) {
            $this->actionError = $response->json('message') ?? 'Could not restore manuscript.';
        }
        $this->load($backend);
    }

    public function render()
    {
        return view('livewire.admin.manuscript-management');
    }
}
