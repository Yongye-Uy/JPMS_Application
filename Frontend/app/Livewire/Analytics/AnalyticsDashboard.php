<?php

namespace App\Livewire\Analytics;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Analytics')]
class AnalyticsDashboard extends Component
{
    public array $journalReport = [];
    public array $reviewerReport = [];
    public array $topArticles = [];

    public function mount(BackendClient $backend)
    {
        $jr = $backend->get('/reports/journal-performance');
        $this->journalReport = $jr->successful() ? $jr->json() : [];

        $rr = $backend->get('/reports/reviewer-performance');
        $this->reviewerReport = $rr->successful() ? $rr->json() : [];

        $articles = $backend->get('/articles', ['per_page' => 100]);
        $data = $articles->successful() ? ($articles->json('data') ?? []) : [];
        usort($data, fn ($a, $b) => ($b['views'] ?? 0) <=> ($a['views'] ?? 0));
        $this->topArticles = array_slice($data, 0, 10);
    }

    public function render()
    {
        return view('livewire.analytics.analytics-dashboard');
    }
}
