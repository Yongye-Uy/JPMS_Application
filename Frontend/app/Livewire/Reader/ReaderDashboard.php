<?php

namespace App\Livewire\Reader;

use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('My Account')]
class ReaderDashboard extends Component
{
    public function isAuthor(): bool
    {
        return AuthenticatedUser::hasRole('Author');
    }

    public function render()
    {
        return view('livewire.reader.reader-dashboard');
    }
}
