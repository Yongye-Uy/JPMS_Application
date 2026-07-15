<?php

namespace App\Livewire\Production;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Production Dashboard')]
class ProductionDashboard extends Component
{
    public string $search = '';
    public array $unassigned = [];
    public array $inDraftIssues = [];
    public bool $loaded = false;

    /**
     * Don't load anything on mount — the page starts empty and the user
     * triggers a load by typing in the search box. This prevents the 502
     * caused by fetching hundreds of records on every page visit.
     */
    public function mount(): void
    {
        // intentionally empty
    }

    public function updatedSearch(): void
    {
        $this->loadData(app(BackendClient::class));
    }

    public function loadData(BackendClient $backend): void
    {
        $query = ['status' => 'Accepted', 'per_page' => 30];
        if ($this->search !== '') {
            $query['q'] = $this->search;
        }

        $accepted = $backend->get('/manuscripts', $query);
        $manuscripts = $accepted->successful() ? ($accepted->json('data') ?? []) : [];

        // Only fetch articles for the manuscripts we just retrieved — avoids
        // a full-table scan. We collect manuscript IDs and filter server-side.
        $manuscriptIds = array_column($manuscripts, 'id');
        $articleByManuscript = collect([]);

        if (! empty($manuscriptIds)) {
            $articles = $backend->get('/articles', [
                'include_unpublished' => 1,
                'per_page' => 30,
                'manuscript_ids' => implode(',', $manuscriptIds),
            ]);
            $articleByManuscript = collect($articles->successful() ? ($articles->json('data') ?? []) : [])
                ->keyBy('manuscript_id');
        }

        $this->unassigned = [];
        $this->inDraftIssues = [];

        foreach ($manuscripts as $m) {
            $article = $articleByManuscript->get($m['id']);
            if (! $article) {
                $this->unassigned[] = $m;
            } elseif (empty($article['published_at'])) {
                $this->inDraftIssues[] = ['manuscript' => $m, 'article' => $article];
            }
        }

        $this->loaded = true;
    }

    public function render()
    {
        return view('livewire.production.production-dashboard');
    }
}
