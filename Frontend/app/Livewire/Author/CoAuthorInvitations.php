<?php

namespace App\Livewire\Author;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Co-Author Invitations')]
class CoAuthorInvitations extends Component
{
    public array $invitations = [];
    public string $message = '';
    public string $error = '';

    public function mount(BackendClient $backend)
    {
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get('/co-author-invitations', ['per_page' => 100]);
        $this->invitations = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function respond(int $invitationId, string $status, BackendClient $backend)
    {
        $this->error = '';

        $payload = ['status' => $status];
        if ($status === 'Accepted') {
            $payload['author_order'] = 2;
        }

        $response = $backend->post("/co-author-invitations/{$invitationId}/respond", $payload);

        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Could not respond to invitation.';

            return;
        }

        $this->message = $status === 'Accepted' ? 'Invitation accepted.' : 'Invitation declined.';
        $this->load($backend);
    }

    public function render()
    {
        return view('livewire.author.co-author-invitations');
    }
}
