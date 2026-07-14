<?php

namespace App\Livewire\Admin;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Admin Dashboard')]
class AdminDashboard extends Component
{
    public int $totalUsers = 0;
    public int $totalJournals = 0;
    public int $totalManuscripts = 0;
    public int $totalArticles = 0;

    public function mount(BackendClient $backend)
    {
        $this->totalUsers = $backend->get('/users', ['per_page' => 1])->json('total') ?? 0;
        $this->totalJournals = $backend->get('/journals', ['per_page' => 1])->json('total') ?? 0;
        $this->totalManuscripts = $backend->get('/manuscripts', ['per_page' => 1])->json('total') ?? 0;
        $this->totalArticles = $backend->get('/articles', ['per_page' => 1, 'include_unpublished' => 1])->json('total') ?? 0;
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard');
    }
}
