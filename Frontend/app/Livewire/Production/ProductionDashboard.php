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
    public array $unassigned = [];
    public array $inDraftIssues = [];

    public function mount(BackendClient $backend)
    {
        $accepted = $backend->get('/manuscripts', ['status' => 'Accepted', 'per_page' => 100]);
        $manuscripts = $accepted->successful() ? ($accepted->json('data') ?? []) : [];

        $articles = $backend->get('/articles', ['include_unpublished' => 1, 'per_page' => 200]);
        $articleByManuscript = collect($articles->successful() ? ($articles->json('data') ?? []) : [])
            ->keyBy('manuscript_id');

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
