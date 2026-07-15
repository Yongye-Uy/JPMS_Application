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

    public function mount(BackendClient $backend)
    {
        $this->load($backend);
    }

    public function updatedSearch(BackendClient $backend)
    {
        $this->load($backend);
    }

    public function updatedStatus(BackendClient $backend)
    {
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $query = array_filter([
            'q'        => $this->search,
            'status'   => $this->status,
            'per_page' => 50,
        ]);

        $response = $backend->get('/manuscripts', $query);
        $all = $response->successful() ? ($response->json('data') ?? []) : [];

        // When no status filter is active, hide Draft & Withdrawn (irrelevant to editors)
        if ($this->status === '') {
            $all = array_values(array_filter($all, fn ($m) => ! in_array($m['status'], ['Draft', 'Withdrawn'])));
        }

        $this->manuscripts = $all;
    }

    public function render()
    {
        return view('livewire.editor.editor-dashboard');
    }
}
