<?php

namespace App\Livewire\Editor;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Find Reviewers')]
class ReviewerSearch extends Component
{
    public string $search = '';
    public array $reviewers = [];

    public string $manuscript_id = '';
    public string $deadline = '';
    public ?int $invitingReviewerId = null;
    public string $inviteMessage = '';
    public string $inviteError = '';

    public array $manuscripts = [];

    public function mount(BackendClient $backend)
    {
        $this->load($backend);
        $this->loadManuscripts($backend);
    }

    private function loadManuscripts(BackendClient $backend): void
    {
        $response = $backend->get('/manuscripts', ['status' => 'Submitted', 'per_page' => 100]);
        $this->manuscripts = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function updatedSearch(BackendClient $backend)
    {
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get('/reviewers', array_filter(['search' => $this->search, 'per_page' => 10]));
        $this->reviewers = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function startInvite(int $reviewerId)
    {
        $this->invitingReviewerId = $reviewerId;
        $this->inviteMessage = '';
        $this->inviteError = '';
    }

    public function invite(BackendClient $backend)
    {
        $this->inviteError = '';

        $this->validate([
            'manuscript_id' => 'required|integer',
            'deadline' => 'required|date',
        ]);

        $response = $backend->post("/manuscripts/{$this->manuscript_id}/review-invitations", [
            'reviewer_id' => $this->invitingReviewerId,
            'deadline' => $this->deadline,
        ]);

        if (! $response->successful()) {
            $this->inviteError = $response->json('message') ?? 'Could not send invitation.';

            return;
        }

        $this->inviteMessage = 'Invitation sent.';
        $this->invitingReviewerId = null;
        $this->manuscript_id = '';
        $this->deadline = '';
    }

    public function render()
    {
        return view('livewire.editor.reviewer-search');
    }
}
