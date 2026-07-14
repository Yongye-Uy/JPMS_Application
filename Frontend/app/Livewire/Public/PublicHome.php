<?php

namespace App\Livewire\Public;

use App\Clients\BackendClient;
use App\Support\JournalOptions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.root')]
#[Title('Search Articles')]
class PublicHome extends Component
{
    public string $field = 'title';
    public string $q = '';
    public string $journal_id = '';
    public string $year = '';

    public array $results = [];
    public array $journals = [];
    public bool $searched = false;

    public int $page = 1;
    public int $perPage = 20;
    public int $lastPage = 1;
    public int $total = 0;

    public function mount(BackendClient $backend)
    {
        $this->journals = JournalOptions::forSelect($backend);
        $this->search($backend);
    }

    public function updatedField(BackendClient $backend)
    {
        $this->page = 1;
        $this->search($backend);
    }

    public function updatedQ(BackendClient $backend)
    {
        $this->page = 1;
        $this->search($backend);
    }

    public function updatedJournalId(BackendClient $backend)
    {
        $this->page = 1;
        $this->search($backend);
    }

    public function updatedYear(BackendClient $backend)
    {
        $this->page = 1;
        $this->search($backend);
    }

    public function previousPage(BackendClient $backend)
    {
        if ($this->page > 1) {
            $this->page--;
            $this->search($backend);
        }
    }

    public function nextPage(BackendClient $backend)
    {
        if ($this->page < $this->lastPage) {
            $this->page++;
            $this->search($backend);
        }
    }

    public function gotoPage(int $page, BackendClient $backend)
    {
        $this->page = max(1, min($page, $this->lastPage));
        $this->search($backend);
    }

    /**
     * Backend's `GET /articles` only supports a single full-text `q` param
     * (see Backend/app/Http/Controllers/Reader/ArticleController::index) —
     * there's no per-field (title/author/keyword/abstract) search. The
     * field selector is kept for the spec'd UI but always feeds the same
     * `q` param. `year`, `page`, and `per_page` are real server-side params
     * (Central-Service paginates with Eloquent's paginate(), 20/page by
     * default) — filtering happens on the full dataset, not just whatever
     * page happened to come back.
     */
    public function search(BackendClient $backend)
    {
        $this->searched = true;

        $query = ['page' => $this->page, 'per_page' => $this->perPage];
        if ($this->q !== '') {
            $query['q'] = $this->q;
        }
        if ($this->journal_id !== '') {
            $query['journal_id'] = $this->journal_id;
        }
        if ($this->year !== '') {
            $query['year'] = $this->year;
        }

        $response = $backend->get('/articles', $query);
        $body = $response->successful() ? $response->json() : [];

        $this->results = $body['data'] ?? [];
        $this->page = $body['current_page'] ?? 1;
        $this->lastPage = $body['last_page'] ?? 1;
        $this->total = $body['total'] ?? count($this->results);
    }

    public function render()
    {
        return view('livewire.public.public-home');
    }
}
