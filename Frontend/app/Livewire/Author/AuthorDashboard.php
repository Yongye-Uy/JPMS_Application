<?php

namespace App\Livewire\Author;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Author Dashboard')]
class AuthorDashboard extends Component
{
    public array $counts = [
        'Draft' => 0, 'Submitted' => 0, 'Under Review' => 0,
        'Revision Required' => 0, 'Accepted' => 0, 'Rejected' => 0,
    ];

    public array $recent = [];

    public function mount(BackendClient $backend)
    {
        $response = $backend->get('/manuscripts', ['author_id' => AuthenticatedUser::id(), 'per_page' => 25]);
        $manuscripts = $response->successful() ? ($response->json('data') ?? []) : [];

        foreach ($manuscripts as $m) {
            $status = $m['status'] ?? 'Draft';
            if (array_key_exists($status, $this->counts)) {
                $this->counts[$status]++;
            }
        }

        usort($manuscripts, fn ($a, $b) => strcmp($b['updated_at'] ?? '', $a['updated_at'] ?? ''));
        $this->recent = array_slice($manuscripts, 0, 5);
    }

    public function render()
    {
        return view('livewire.author.author-dashboard');
    }
}
