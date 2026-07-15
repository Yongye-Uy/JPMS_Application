<?php

namespace App\Livewire\Author;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('My Submissions')]
class MySubmissions extends Component
{
    public string $q = '';
    public string $status = '';

    public function submissions(BackendClient $backend)
    {
        $query = ['author_id' => AuthenticatedUser::id(), 'per_page' => 50];
        if ($this->q !== '') {
            $query['q'] = $this->q;
        }
        if ($this->status !== '') {
            $query['status'] = $this->status;
        }

        $response = $backend->get('/manuscripts', $query);

        return $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function render(BackendClient $backend)
    {
        return view('livewire.author.my-submissions', ['submissions' => $this->submissions($backend)]);
    }
}
