<?php

namespace App\Livewire\Reviewer;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.dashboard')]
#[Title('Submit Review')]
class SubmitReview extends Component
{
    use WithFileUploads;

    public const CRITERIA = ['Originality', 'Methodology', 'Clarity', 'Significance', 'References'];
    public const RECOMMENDATIONS = ['Accept', 'Minor Revision', 'Major Revision', 'Reject'];

    public int $invitationId;
    public array $invitation = [];
    public bool $notFound = false;
    public bool $locked = false;

    public array $scores = [];
    public string $recommendation = '';
    public string $comments_to_author = '';
    public string $comments_to_editor = '';
    public $annotated_file = null;

    public string $error = '';
    public string $message = '';

    public function mount(int $invitationId, BackendClient $backend)
    {
        $this->invitationId = $invitationId;

        foreach (self::CRITERIA as $criterion) {
            $this->scores[$criterion] = 3;
        }

        $response = $backend->get("/review-invitations/{$this->invitationId}");
        if (! $response->successful()) {
            $this->notFound = true;

            return;
        }
        $this->invitation = $response->json();
        $this->locked = ($this->invitation['status'] ?? '') === 'Completed';

        if ($this->locked && ! empty($this->invitation['review'])) {
            $review = $this->invitation['review'];
            $this->recommendation = $review['recommendation'] ?? '';
            $this->comments_to_author = $review['comments_to_author'] ?? '';
            $this->comments_to_editor = $review['comments_to_editor'] ?? '';
            foreach ($review['scores'] ?? [] as $score) {
                $this->scores[$score['criterion']] = $score['score'];
            }
        }
    }

    public function submit(BackendClient $backend)
    {
        $this->error = '';
        $this->message = '';

        $this->validate([
            'recommendation' => 'required|string|in:'.implode(',', self::RECOMMENDATIONS),
            'comments_to_author' => 'required|string',
            'comments_to_editor' => 'nullable|string',
        ]);

        $scores = [];
        foreach (self::CRITERIA as $criterion) {
            $scores[] = ['criterion' => $criterion, 'score' => (int) $this->scores[$criterion]];
        }

        $files = $this->annotated_file ? ['annotated_file' => $this->annotated_file] : [];

        $response = $backend->postMultipart("/review-invitations/{$this->invitationId}/reviews", [
            'recommendation' => $this->recommendation,
            'comments_to_author' => $this->comments_to_author,
            'comments_to_editor' => $this->comments_to_editor ?: '',
            'scores' => $scores,
        ], $files);

        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Could not submit review.';

            return;
        }

        return redirect()->route('reviewer.dashboard');
    }

    public function render()
    {
        return view('livewire.reviewer.submit-review');
    }
}
