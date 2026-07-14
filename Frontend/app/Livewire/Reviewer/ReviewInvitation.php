<?php

namespace App\Livewire\Reviewer;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Review Invitation')]
class ReviewInvitation extends Component
{
    public int $invitationId;
    public array $invitation = [];
    public bool $notFound = false;

    public string $declined_reason = '';
    public string $error = '';

    public function mount(int $invitationId, BackendClient $backend)
    {
        $this->invitationId = $invitationId;
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get("/review-invitations/{$this->invitationId}");
        if (! $response->successful()) {
            $this->notFound = true;

            return;
        }
        $this->invitation = $response->json();
    }

    public function accept(BackendClient $backend)
    {
        $this->error = '';
        $response = $backend->post("/review-invitations/{$this->invitationId}/respond", ['status' => 'Accepted']);

        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Could not accept invitation.';

            return;
        }

        return redirect()->route('reviewer.reviews.submit', ['invitationId' => $this->invitationId]);
    }

    public function decline(BackendClient $backend)
    {
        $this->error = '';
        $this->validate(['declined_reason' => 'required|string'], [], ['declined_reason' => 'reason']);

        $response = $backend->post("/review-invitations/{$this->invitationId}/respond", [
            'status' => 'Declined',
            'declined_reason' => $this->declined_reason,
        ]);

        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Could not decline invitation.';

            return;
        }

        return redirect()->route('reviewer.dashboard');
    }

    public function render()
    {
        return view('livewire.reviewer.review-invitation');
    }
}
