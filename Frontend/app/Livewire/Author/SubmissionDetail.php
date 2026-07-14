<?php

namespace App\Livewire\Author;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.dashboard')]
#[Title('Submission Detail')]
class SubmissionDetail extends Component
{
    use WithFileUploads;

    public int $submissionId;
    public array $manuscript = [];
    public bool $notFound = false;

    public string $tab = 'overview';

    // Withdraw
    public string $withdraw_reason = '';
    public string $withdrawError = '';

    // Resubmit (from Withdrawn)
    public string $resubmitError = '';

    // Delete (Draft only)
    public string $deleteError = '';

    // Upload version
    public $main_file = null;
    public string $response_note = '';
    public string $uploadError = '';
    public string $uploadMessage = '';

    // Invite co-author
    public string $coauthor_search = '';
    public array $coauthor_results = [];
    public string $invited_author_id_manual = '';
    public string $coauthorMessage = '';
    public string $coauthorError = '';

    public string $submitMessage = '';
    public string $submitError = '';

    // Set main version
    public string $setMainMessage = '';
    public string $setMainError = '';

    public function mount(int $submissionId, BackendClient $backend)
    {
        $this->submissionId = $submissionId;
        $this->load($backend);
    }

    private function load(BackendClient $backend): void
    {
        $response = $backend->get("/manuscripts/{$this->submissionId}");
        if (! $response->successful()) {
            $this->notFound = true;

            return;
        }
        $this->manuscript = $response->json();
    }

    public function isOwner(): bool
    {
        return ($this->manuscript['author_id'] ?? null) === AuthenticatedUser::id();
    }

    public function isCoAuthor(): bool
    {
        return collect($this->manuscript['authors'] ?? [])->contains(fn ($a) => (int) $a['user_id'] === AuthenticatedUser::id());
    }

    public function setMainVersion(BackendClient $backend, int $versionId)
    {
        $this->setMainError = '';
        $this->setMainMessage = '';

        if (! $this->isOwner()) {
            $this->setMainError = 'Only the primary author can set the main version.';

            return;
        }

        if (! in_array($this->manuscript['status'] ?? null, ['Draft', 'Revision Required'], true)) {
            $this->setMainError = 'The main version can only be changed while the manuscript is in Draft or Revision Required status.';

            return;
        }

        $response = $backend->post("/manuscripts/{$this->submissionId}/versions/{$versionId}/set-main");
        if (! $response->successful()) {
            $this->setMainError = $response->json('message') ?? 'Could not update main version.';

            return;
        }

        $this->setMainMessage = 'Main version updated.';
        $this->load($backend);
    }

    public function submitForReview(BackendClient $backend)
    {
        $this->submitError = '';
        $this->submitMessage = '';

        if (! in_array($this->manuscript['status'] ?? null, ['Draft', 'Revision Required'], true)) {
            $this->submitError = 'Only manuscripts in Draft or Revision Required status can be submitted.';

            return;
        }

        if (empty($this->manuscript['title']) || empty($this->manuscript['abstract'])) {
            $this->submitError = 'Title and abstract must be present before submitting.';

            return;
        }

        $response = $backend->post("/manuscripts/{$this->submissionId}/submit");
        if (! $response->successful()) {
            $this->submitError = $response->json('message') ?? 'Could not submit.';

            return;
        }

        $this->submitMessage = 'Submitted for review.';
        $this->load($backend);
    }

    public function withdraw(BackendClient $backend)
    {
        $this->withdrawError = '';

        $this->validate(['withdraw_reason' => 'required|string'], [], ['withdraw_reason' => 'reason']);

        $response = $backend->post("/manuscripts/{$this->submissionId}/withdraw", ['reason' => $this->withdraw_reason]);
        if (! $response->successful()) {
            $this->withdrawError = $response->json('message') ?? 'Could not withdraw.';

            return;
        }

        $this->withdraw_reason = '';
        $this->load($backend);
    }

    public function resubmitManuscript(BackendClient $backend)
    {
        $this->resubmitError = '';

        if (! $this->isOwner()) {
            $this->resubmitError = 'Only the primary author can resubmit this manuscript.';

            return;
        }

        $response = $backend->post("/manuscripts/{$this->submissionId}/resubmit");
        if (! $response->successful()) {
            $this->resubmitError = $response->json('message') ?? 'Could not resubmit.';

            return;
        }

        $this->load($backend);
    }

    public function deleteManuscript(BackendClient $backend)
    {
        $this->deleteError = '';

        if (! $this->isOwner()) {
            $this->deleteError = 'Only the primary author can delete this manuscript.';

            return;
        }

        if (($this->manuscript['status'] ?? null) !== 'Draft') {
            $this->deleteError = 'Only Draft manuscripts can be deleted.';

            return;
        }

        $response = $backend->delete("/manuscripts/{$this->submissionId}");
        if (! $response->successful()) {
            $this->deleteError = $response->json('message') ?? 'Could not delete manuscript.';

            return;
        }

        return redirect()->route('author.submissions');
    }

    public function uploadVersion(BackendClient $backend)
    {
        $this->uploadError = '';
        $this->uploadMessage = '';

        $this->validate([
            'main_file' => 'required|file|mimes:pdf',
            'response_note' => 'nullable|string',
        ]);

        $response = $backend->postMultipart(
            "/manuscripts/{$this->submissionId}/versions",
            ['response_note' => $this->response_note ?: ''],
            ['main_file' => $this->main_file]
        );

        if (! $response->successful()) {
            $this->uploadError = $response->json('message') ?? 'Could not upload file.';

            return;
        }

        $this->main_file = null;
        $this->response_note = '';
        $this->uploadMessage = 'File uploaded.';
        $this->load($backend);
    }

    public function searchCoAuthors(BackendClient $backend)
    {
        $response = $backend->get('/co-authors/search', array_filter(['search' => $this->coauthor_search]));
        $results = $response->successful() ? ($response->json('data') ?? []) : [];

        $existingIds = collect($this->manuscript['authors'] ?? [])->map(fn ($a) => (int) $a['user_id'])
            ->merge(
                collect($this->manuscript['co_author_invitations'] ?? [])
                    ->filter(fn ($inv) => in_array($inv['status'], ['Pending', 'Accepted'], true))
                    ->map(fn ($inv) => (int) $inv['invited_author_id'])
            )
            ->all();

        $this->coauthor_results = collect($results)
            ->reject(fn ($u) => in_array((int) $u['id'], $existingIds, true))
            ->values()
            ->all();
    }

    public function updatedTab(BackendClient $backend): void
    {
        if ($this->tab === 'coauthors' && empty($this->coauthor_results)) {
            $this->searchCoAuthors($backend);
        }
    }

    public function inviteCoAuthor(BackendClient $backend, ?int $userId = null)
    {
        $this->coauthorMessage = '';
        $this->coauthorError = '';

        $invitedId = $userId ?: (int) $this->invited_author_id_manual;

        if (! $invitedId) {
            $this->coauthorError = 'Choose a co-author or enter a user ID.';

            return;
        }

        $response = $backend->post("/manuscripts/{$this->submissionId}/co-authors/invite", ['invited_author_id' => $invitedId]);

        if (! $response->successful()) {
            $this->coauthorError = $response->json('message') ?? 'Could not invite co-author.';

            return;
        }

        $this->coauthorMessage = 'Invitation sent.';
        $this->invited_author_id_manual = '';
        $this->coauthor_search = '';
        $this->coauthor_results = [];
        $this->load($backend);
    }

    public function render()
    {
        return view('livewire.author.submission-detail');
    }
}
