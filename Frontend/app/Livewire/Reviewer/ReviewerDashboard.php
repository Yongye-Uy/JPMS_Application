<?php

namespace App\Livewire\Reviewer;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Reviewer Dashboard')]
class ReviewerDashboard extends Component
{
    public array $invitations = [];

    public function mount(BackendClient $backend)
    {
        $response = $backend->get('/review-invitations', ['per_page' => 100]);
        $this->invitations = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function pending(): array
    {
        return array_values(array_filter($this->invitations, fn ($i) => $i['status'] === 'Pending'));
    }

    public function active(): array
    {
        return array_values(array_filter($this->invitations, fn ($i) => in_array($i['status'], ['Accepted', 'In Progress'])));
    }

    public function completed(): array
    {
        return array_values(array_filter($this->invitations, fn ($i) => $i['status'] === 'Completed'));
    }

    public function overdue(): array
    {
        return array_values(array_filter(
            $this->active(),
            fn ($i) => ! empty($i['deadline']) && strtotime($i['deadline']) < time()
        ));
    }

    public function render()
    {
        return view('livewire.reviewer.reviewer-dashboard');
    }
}
