<?php

namespace App\Livewire;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Profile')]
class Profile extends Component
{
    public string $full_name = '';
    public string $affiliation = '';
    public string $country = '';
    public string $contact_info = '';

    public string $old_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public string $profileMessage = '';
    public string $passwordMessage = '';
    public string $passwordError = '';

    public function mount(BackendClient $backend)
    {
        $user = $backend->get('/profile')->json();
        $this->full_name = $user['full_name'] ?? '';
        $this->affiliation = $user['affiliation'] ?? '';
        $this->country = $user['country'] ?? '';
        $this->contact_info = $user['contact_info'] ?? '';
    }

    public function updateProfile(BackendClient $backend)
    {
        $this->validate(['full_name' => 'required|string']);

        $response = $backend->patch('/profile', [
            'full_name' => $this->full_name,
            'affiliation' => $this->affiliation ?: null,
            'country' => $this->country ?: null,
            'contact_info' => $this->contact_info ?: null,
        ]);

        if ($response->successful()) {
            AuthenticatedUser::store($response->json());
            $this->profileMessage = 'Profile updated.';
        }
    }

    public function changePassword(BackendClient $backend)
    {
        $this->passwordMessage = '';
        $this->passwordError = '';

        $this->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        $response = $backend->post('/password/change', [
            'old_password' => $this->old_password,
            'new_password' => $this->new_password,
            'new_password_confirmation' => $this->new_password_confirmation,
        ]);

        if (! $response->successful()) {
            $this->passwordError = $response->json('message') ?? 'Could not change password.';

            return;
        }

        $this->old_password = $this->new_password = $this->new_password_confirmation = '';
        $this->passwordMessage = 'Password changed.';
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
