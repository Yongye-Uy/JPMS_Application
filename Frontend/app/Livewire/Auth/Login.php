<?php

namespace App\Livewire\Auth;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Login')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public string $error = '';

    public function submit(BackendClient $backend)
    {
        $this->error = '';

        $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $response = $backend->post('/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Invalid credentials.';

            return;
        }

        $backend->setToken($response->json('token'));
        AuthenticatedUser::store($response->json('user'));

        $returnTo = session()->pull('return_to');
        if ($returnTo) {
            return redirect()->to($returnTo);
        }

        return redirect()->route('dashboard.home');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
