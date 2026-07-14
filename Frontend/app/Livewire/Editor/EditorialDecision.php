<?php

namespace App\Livewire\Editor;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Editorial Decision')]
class EditorialDecision extends Component
{
    public const DECISIONS = ['Accept', 'Return to Edit', 'Minor Revision', 'Major Revision', 'Reject', 'Desk Reject'];
    public const LETTER_REQUIRED = ['Return to Edit', 'Minor Revision', 'Major Revision'];

    public const DECIDABLE_STATUSES = ['Submitted', 'Under Review', 'Ready for Decision'];

    public int $submissionId;
    public array $manuscript = [];
    public bool $notFound = false;
    public bool $locked = false;

    public string $decision = '';
    public string $decision_letter = '';
    public string $error = '';

    public function mount(int $submissionId, BackendClient $backend)
    {
        $this->submissionId = $submissionId;
        $response = $backend->get("/manuscripts/{$this->submissionId}");
        if (! $response->successful()) {
            $this->notFound = true;

            return;
        }
        $this->manuscript = $response->json();
        $this->locked = ! in_array($this->manuscript['status'] ?? null, self::DECIDABLE_STATUSES, true);
    }

    public function submit(BackendClient $backend)
    {
        $this->error = '';

        $rules = ['decision' => 'required|string|in:'.implode(',', self::DECISIONS)];
        if (in_array($this->decision, self::LETTER_REQUIRED, true)) {
            $rules['decision_letter'] = 'required|string';
        }
        $this->validate($rules);

        $response = $backend->post("/manuscripts/{$this->submissionId}/decisions", [
            'decision' => $this->decision,
            'decision_letter' => $this->decision_letter ?: null,
        ]);

        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Could not record decision.';

            return;
        }

        return redirect()->route('editor.submissions.show', ['submissionId' => $this->submissionId]);
    }

    public function render()
    {
        return view('livewire.editor.editorial-decision');
    }
}
