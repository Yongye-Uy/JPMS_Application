<?php

namespace App\Livewire\Production;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Manage Issue')]
class ManageIssue extends Component
{
    public int $issueId;
    public array $issue = [];
    public bool $notFound = false;

    public array $availableManuscripts = [];

    public string $manuscript_id = '';
    public string $page_start = '';
    public string $page_end = '';
    public string $addMessage = '';
    public string $addError = '';
    public string $publishError = '';

    public function mount(int $issueId, BackendClient $backend)
    {
        $this->issueId = $issueId;
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get("/issues/{$this->issueId}");
        if (! $response->successful()) {
            $this->notFound = true;

            return;
        }
        $this->issue = $response->json();

        $accepted = $backend->get('/manuscripts', ['status' => 'Accepted', 'per_page' => 25]);
        $manuscripts = $accepted->successful() ? ($accepted->json('data') ?? []) : [];
        $alreadyIn = collect($this->issue['articles'] ?? [])->pluck('manuscript_id')->all();
        $this->availableManuscripts = array_values(array_filter($manuscripts, fn ($m) => ! in_array($m['id'], $alreadyIn)));
    }

    public function addArticle(BackendClient $backend)
    {
        $this->addError = '';
        $this->validate([
            'manuscript_id' => 'required|integer',
            'page_start' => 'nullable|integer',
            'page_end' => 'nullable|integer',
        ]);

        $response = $backend->post("/issues/{$this->issueId}/articles", [
            'manuscript_id' => (int) $this->manuscript_id,
            'page_start' => $this->page_start ?: null,
            'page_end' => $this->page_end ?: null,
        ]);

        if (! $response->successful()) {
            $this->addError = $response->json('message') ?? 'Could not add article.';

            return;
        }

        $this->manuscript_id = $this->page_start = $this->page_end = '';
        $this->addMessage = 'Article added.';
        $this->load($backend);
    }

    public function removeArticle(int $articleId, BackendClient $backend)
    {
        $backend->delete("/articles/{$articleId}");
        $this->load($backend);
    }

    public function publish(BackendClient $backend)
    {
        $this->publishError = '';
        $response = $backend->post("/issues/{$this->issueId}/publish");

        if (! $response->successful()) {
            $this->publishError = $response->json('message') ?? 'Could not publish issue.';

            return;
        }

        $this->load($backend);
    }

    public function render()
    {
        return view('livewire.production.manage-issue');
    }
}
