<?php

namespace App\Livewire\Editor;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Review Monitoring')]
class ReviewMonitoring extends Component
{
    public int $submissionId;
    public array $manuscript = [];
    public bool $notFound = false;

    public string $reviewer_id = '';
    public string $deadline = '';
    public string $inviteMessage = '';
    public string $inviteError = '';

    public string $reviewer_search = '';
    public array $reviewer_results = [];
    public ?int $selectedReviewerId = null;
    public string $selectedReviewerName = '';

    public function mount(int $submissionId, BackendClient $backend)
    {
        $this->submissionId = $submissionId;
        $this->load($backend);
        $this->searchReviewers($backend);
    }

    public function searchReviewers(BackendClient $backend)
    {
        $response = $backend->get('/reviewers', array_filter(['search' => $this->reviewer_search]));
        $this->reviewer_results = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function pickReviewer(int $reviewerId, string $reviewerName)
    {
        $this->reviewer_id = (string) $reviewerId;
        $this->selectedReviewerId = $reviewerId;
        $this->selectedReviewerName = $reviewerName;
        $this->reviewer_results = [];
        $this->reviewer_search = '';
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

    public function inviteReviewer(BackendClient $backend)
    {
        $this->inviteError = '';

        $this->validate([
            'reviewer_id' => 'required|integer',
            'deadline' => 'required|date',
        ]);

        $response = $backend->post("/manuscripts/{$this->submissionId}/review-invitations", [
            'reviewer_id' => (int) $this->reviewer_id,
            'deadline' => $this->deadline,
        ]);

        if (! $response->successful()) {
            $this->inviteError = $response->json('message') ?? 'Could not invite reviewer.';

            return;
        }

        $this->reviewer_id = '';
        $this->deadline = '';
        $this->selectedReviewerId = null;
        $this->selectedReviewerName = '';
        $this->inviteMessage = 'Reviewer invited.';
        $this->load($backend);
    }

    public function render()
    {
        return view('livewire.editor.review-monitoring');
    }
}
