<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function verifyCredentials(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);

        return $this->relay($this->central->post('/auth/verify-credentials', $data));
    }

    public function issueToken(Request $request)
    {
        $data = $request->validate(['user_id' => 'required|integer', 'name' => 'sometimes|string']);

        return $this->relay($this->central->post('/auth/tokens', $data));
    }

    public function introspectToken(Request $request)
    {
        $data = $request->validate(['token' => 'required|string']);

        return $this->relay($this->central->post('/auth/tokens/introspect', $data));
    }

    public function revokeToken(int $tokenId)
    {
        return $this->relay($this->central->delete("/auth/tokens/{$tokenId}"));
    }
}
