<?php

namespace App\Http\Controllers\Admin;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** Module 1 (admin): search/list users. */
    public function index(Request $request)
    {
        $response = $this->api->get('/users', $request->query());

        return response()->json($response->json(), $response->status());
    }

    /** Module 1 (admin): create a user (with roles). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string',
            'affiliation' => 'nullable|string',
            'country' => 'nullable|string',
            'contact_info' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'roles' => 'sometimes|array',
        ]);

        $response = $this->api->post('/users', $data);

        return response()->json($response->json(), $response->status());
    }

    public function show(int $id)
    {
        $response = $this->api->get("/users/{$id}");

        return response()->json($response->json(), $response->status());
    }

    /** Module 1 (admin): update a user, including deactivation via is_active. */
    public function update(Request $request, int $id)
    {
        $user = $request->user();

        $data = $request->validate([
            'full_name' => 'sometimes|string',
            'affiliation' => 'nullable|string',
            'country' => 'nullable|string',
            'contact_info' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'password' => 'sometimes|string|min:6',
        ]);

        // Guard against an admin deactivating their own account.
        if ($id === $user->id && array_key_exists('is_active', $data) && ! $data['is_active']) {
            return response()->json(['message' => 'You cannot deactivate your own account.'], 422);
        }

        $response = $this->api->patch("/users/{$id}", $data);

        return response()->json($response->json(), $response->status());
    }
}
