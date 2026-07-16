<?php

namespace App\Livewire\Admin;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Audit Log')]
class AuditLog extends Component
{
    public array $entries = [];
    public int $perPage = 25;
    public int $page = 1;

    public function mount(BackendClient $backend)
    {
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get('/audit-log', [
            'per_page' => $this->perPage,
            'page' => $this->page,
        ]);
        $this->entries = $response->successful() ? ($response->json('data') ?? []) : [];
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
        if (count($this->entries) === $this->perPage) {
            $this->page++;
            $this->load($backend);
        }
    }

    public function gotoPage(int $page): void
    {
        $this->page = $page;
        $backend = app(BackendClient::class);
        $this->load($backend);
    }

    public function render()
    {
        return view('livewire.admin.audit-log');
    }
}
