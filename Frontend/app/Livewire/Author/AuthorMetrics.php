<?php

namespace App\Livewire\Author;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('My Metrics')]
class AuthorMetrics extends Component
{
    public array $articles = [];

    public function mount(BackendClient $backend)
    {
        $response = $backend->get('/author/metrics');
        $this->articles = $response->successful() ? ($response->json('articles') ?? []) : [];
    }

    public function totalViews(): int
    {
        return array_sum(array_column($this->articles, 'views'));
    }

    public function totalDownloads(): int
    {
        return array_sum(array_column($this->articles, 'downloads'));
    }

    public function totalCitations(): int
    {
        return array_sum(array_column($this->articles, 'citations_count'));
    }

    public function render()
    {
        return view('livewire.author.author-metrics');
    }
}
