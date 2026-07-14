<?php

namespace App\Livewire\Reviewer;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Review History')]
class ReviewHistory extends Component
{
    public array $invitations = [];

    public function mount(BackendClient $backend)
    {
        $response = $backend->get('/review-invitations', ['per_page' => 100]);
        $this->invitations = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function render()
    {
        return view('livewire.reviewer.review-history');
    }
}
