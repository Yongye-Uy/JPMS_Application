<?php

namespace App\Livewire\Author;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Co-Authored Submissions')]
class CoAuthoredSubmissions extends Component
{
    public array $manuscripts = [];

    public function mount(BackendClient $backend)
    {
        $response = $backend->get('/manuscripts', ['co_author_id' => AuthenticatedUser::id(), 'per_page' => 100]);
        $this->manuscripts = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function render()
    {
        return view('livewire.author.co-authored-submissions');
    }
}
