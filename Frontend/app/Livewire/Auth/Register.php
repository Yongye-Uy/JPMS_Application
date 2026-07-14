<?php

namespace App\Livewire\Auth;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Register')]
class Register extends Component
{
    public string $accountType = 'Author';
    public string $full_name = '';
    public string $email = '';
    public string $affiliation = '';
    public string $country = '';
    public string $contact_info = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $error = '';

    /** Self-service registration is Reader/Author only — Reviewer/Editor/Admin are admin-granted (see admin.users). */
    public function submit(BackendClient $backend)
    {
        $this->error = '';

        $this->validate([
            'accountType' => 'required|in:Reader,Author',
            'full_name' => 'required|string',
            'email' => 'required|email',
            'affiliation' => 'nullable|string',
            'country' => 'nullable|string',
            'contact_info' => 'nullable|string',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        $response = $backend->post('/register', [
            'account_type' => $this->accountType,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'affiliation' => $this->affiliation ?: null,
            'country' => $this->country ?: null,
            'contact_info' => $this->contact_info ?: null,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ]);

        if (! $response->successful()) {
            $this->error = $response->json('message') ?? 'Could not register.';

            return;
        }

        $login = $backend->post('/login', ['email' => $this->email, 'password' => $this->password]);
        $backend->setToken($login->json('token'));
        AuthenticatedUser::store($login->json('user'));

        return redirect()->route('dashboard.home');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
