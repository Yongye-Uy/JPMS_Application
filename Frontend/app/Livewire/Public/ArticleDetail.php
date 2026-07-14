<?php

namespace App\Livewire\Public;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.root')]
#[Title('Article')]
class ArticleDetail extends Component
{
    public int $articleId;
    public array $article = [];
    public bool $notFound = false;

    public function mount(int $articleId, BackendClient $backend)
    {
        $this->articleId = $articleId;

        $response = $backend->get("/articles/{$articleId}");
        if (! $response->successful()) {
            $this->notFound = true;

            return;
        }

        $this->article = $response->json();

        // Records the view event server-side (feeds the all-time total shown
        // in Stats); the local count is bumped optimistically below since
        // $this->article was fetched just before this event was recorded.
        $track = $backend->post("/articles/{$articleId}/metrics/track", ['event' => 'view']);
        if ($track->successful()) {
            $this->article['views'] = ($this->article['views'] ?? 0) + 1;
        }
    }

    public function isLoggedIn(): bool
    {
        return AuthenticatedUser::check();
    }

    public function trackCitation(BackendClient $backend)
    {
        $track = $backend->post("/articles/{$this->articleId}/metrics/track", ['event' => 'citation']);
        if ($track->successful()) {
            $this->article['citations_count'] = ($this->article['citations_count'] ?? 0) + 1;
        }
    }

    /** Called a moment after Download is clicked — that click opens the file in a separate tab/request, so this component has no direct way to learn the download (and its server-side counter increment) happened; bumps the displayed total optimistically instead. */
    public function refreshMetrics()
    {
        $this->article['downloads'] = ($this->article['downloads'] ?? 0) + 1;
    }

    public function render()
    {
        return view('livewire.public.article-detail');
    }
}
