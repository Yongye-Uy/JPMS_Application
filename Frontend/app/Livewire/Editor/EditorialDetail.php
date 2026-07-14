<?php

namespace App\Livewire\Editor;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Manuscript Detail')]
class EditorialDetail extends Component
{
    public int $submissionId;
    public array $manuscript = [];
    public bool $notFound = false;

    public function mount(int $submissionId, BackendClient $backend)
    {
        $this->submissionId = $submissionId;
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get("/manuscripts/{$this->submissionId}");
        if (! $response->successful()) {
            $this->notFound = true;

            return;
        }
        $this->manuscript = $response->json();
    }

    public function render()
    {
        return view('livewire.editor.editorial-detail');
    }
}
