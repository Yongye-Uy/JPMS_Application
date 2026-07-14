<?php

namespace App\Livewire\Editor;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Review Detail')]
class ViewReviewDetail extends Component
{
    public int $reviewId;
    public array $review = [];
    public bool $notFound = false;

    public function mount(int $reviewId, BackendClient $backend)
    {
        $this->reviewId = $reviewId;
        $response = $backend->get("/reviews/{$this->reviewId}");
        if (! $response->successful()) {
            $this->notFound = true;

            return;
        }
        $this->review = $response->json();
    }

    public function render()
    {
        return view('livewire.editor.view-review-detail');
    }
}
