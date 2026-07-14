<?php

namespace App\Livewire\Admin;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Manuscript Detail (Admin)')]
class ManuscriptDetail extends Component
{
    public int $submissionId;
    public array $manuscript = [];
    public bool $notFound = false;

    public string $return_reason = '';
    public string $message = '';
    public string $error = '';

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

    public function returnToAuthor(BackendClient $backend)
    {
        $this->error = '';
        $this->validate(['return_reason' => 'required|string'], [], ['return_reason' => 'reason']);

        $response = $backend->post("/manuscripts/{$this->submissionId}/return", ['reason' => $this->return_reason]);
        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Could not return manuscript.';

            return;
        }

        $this->return_reason = '';
        $this->message = 'Returned to author.';
        $this->load($backend);
    }

    public function archive(BackendClient $backend)
    {
        $backend->post("/manuscripts/{$this->submissionId}/archive");
        $this->message = 'Archived.';
        $this->load($backend);
    }

    public function restore(BackendClient $backend)
    {
        $backend->post("/manuscripts/{$this->submissionId}/restore");
        $this->message = 'Restored.';
        $this->load($backend);
    }

    public function render()
    {
        return view('livewire.admin.manuscript-detail');
    }
}
