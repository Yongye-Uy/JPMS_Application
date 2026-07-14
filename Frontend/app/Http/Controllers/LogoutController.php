<?php

namespace App\Http\Controllers;

use App\Clients\BackendClient;
use App\Support\AuthenticatedUser;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request, BackendClient $backend)
    {
        $backend->post('/logout');
        $backend->setToken(null);
        AuthenticatedUser::clear();

        return redirect()->route('public.home');
    }
}
