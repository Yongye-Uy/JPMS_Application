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
    public int $page = 1;
    public int $lastPage = 1;
    public int $total = 0;

    public function mount(BackendClient $backend): void
    {
        $this->loadData($backend);
    }

    public function updatedSearch(BackendClient $backend): void
    {
        $this->page = 1;
        $this->loadData($backend);
    }

    public function nextPage(BackendClient $backend): void
    {
        if ($this->page < $this->lastPage) {
            $this->page++;
            $this->loadData($backend);
        }
    }

    public function prevPage(BackendClient $backend): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->loadData($backend);
        }
    }

    public function gotoPage(BackendClient $backend, int $p): void
    {
        $this->page = $p;
        $this->loadData($backend);
    }

    public function loadData(BackendClient $backend): void
    {
        $query = ['status' => 'Accepted', 'per_page' => 25, 'page' => $this->page];
        if ($this->search !== '') {
            $query['q'] = $this->search;
        }

        $accepted = $backend->get('/manuscripts', $query);
        $json = $accepted->successful() ? $accepted->json() : [];
        $manuscripts = $json['data'] ?? [];

        $this->lastPage = $json['last_page'] ?? 1;
        $this->total    = $json['total'] ?? count($manuscripts);

        $manuscriptIds = array_column($manuscripts, 'id');
        $articleByManuscript = collect([]);

        if (! empty($manuscriptIds)) {
            $articles = $backend->get('/articles', [
                'include_unpublished' => 1,
                'per_page' => 25,
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
    }

    public function render()
    {
        return view('livewire.production.production-dashboard');
    }
}
