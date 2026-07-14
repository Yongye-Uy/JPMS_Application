<?php

namespace App\Http\Controllers;

use App\Clients\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** Module 1 function 3: Login. */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $verify = $this->api->post('/auth/verify-credentials', $data);

        if (! $verify->successful()) {
            return response()->json(['message' => $verify->json('message') ?? 'Invalid credentials.'], $verify->status());
        }

        $user = $verify->json('user');

        $token = $this->api->post('/auth/tokens', ['user_id' => $user['id'], 'name' => 'frontend-session']);

        if (! $token->successful()) {
            return response()->json(['message' => 'Could not start session.'], 500);
        }

        return response()->json(['token' => $token->json('token'), 'user' => $user]);
    }

    /** Module 1 function 2: Register (self-service — Reader/Author only, global role). */
    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string',
            'affiliation' => 'nullable|string',
            'country' => 'nullable|string',
            'contact_info' => 'nullable|string',
            'account_type' => 'required|string|in:Reader,Author',
        ]);

        $response = $this->api->post('/users', [
            'email' => $data['email'],
            'password' => $data['password'],
            'full_name' => $data['full_name'],
            'affiliation' => $data['affiliation'] ?? null,
            'country' => $data['country'] ?? null,
            'contact_info' => $data['contact_info'] ?? null,
            'roles' => [['role_name' => $data['account_type'], 'journal_id' => null]],
        ]);

        return response()->json($response->json(), $response->status());
    }

    /** Module 1 function 3: Logout — revokes the token at Central-Service and evicts Backend's own cache. */
    public function logout(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();
        $token = $request->bearerToken();

        if ($user) {
            $this->api->delete("/auth/tokens/{$user->tokenId}");
        }
        if ($token) {
            Cache::forget('token:'.hash('sha256', $token));
        }

        return response()->json(null, 204);
    }

    /** Module 1 function 4 (partial) + 5: Change password (old password required, matches prototype — not an email/token reset flow). */
    public function changePassword(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $verify = $this->api->post('/auth/verify-credentials', ['email' => $user->email, 'password' => $data['old_password']]);

        if (! $verify->successful()) {
            return response()->json(['message' => 'Old password is incorrect.'], 422);
        }

        $response = $this->api->patch("/users/{$user->id}", ['password' => $data['new_password']]);

        return response()->json($response->json(), $response->status());
    }

    /** Module 1 function 4: View/update own profile. */
    public function profile(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        if ($request->isMethod('get')) {
            return response()->json($this->api->get("/users/{$user->id}")->json());
        }

        $data = $request->validate([
            'full_name' => 'sometimes|string',
            'affiliation' => 'nullable|string',
            'country' => 'nullable|string',
            'contact_info' => 'nullable|string',
        ]);

        $response = $this->api->patch("/users/{$user->id}", $data);

        return response()->json($response->json(), $response->status());
    }
}
