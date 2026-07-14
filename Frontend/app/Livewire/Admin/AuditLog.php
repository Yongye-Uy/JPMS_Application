<?php

namespace App\Livewire\Admin;

use App\Clients\BackendClient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Audit Log')]
class AuditLog extends Component
{
    public array $entries = [];

    public function mount(BackendClient $backend)
    {
        $response = $backend->get('/audit-log', ['per_page' => 100]);
        $this->entries = $response->successful() ? ($response->json('data') ?? []) : [];
    }

    public function render()
    {
        return view('livewire.admin.audit-log');
    }
}
