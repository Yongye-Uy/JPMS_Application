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
    public array $manuscripts = [];

    public function mount(BackendClient $backend)
    {
        $this->load($backend);
    }

    public function updatedSearch(BackendClient $backend)
    {
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get('/manuscripts', array_filter(['q' => $this->search, 'per_page' => 100]));
        $all = $response->successful() ? ($response->json('data') ?? []) : [];
        $this->manuscripts = array_values(array_filter($all, fn ($m) => ! in_array($m['status'], ['Draft', 'Withdrawn'])));
    }

    public function render()
    {
        return view('livewire.editor.editor-dashboard');
    }
}
