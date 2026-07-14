<?php

namespace App\Http\Controllers\Admin;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** Module 1 (admin): assign a role to a user. */
    public function store(Request $request, int $id)
    {
        $data = $request->validate([
            'role_name' => 'required|string',
            'journal_id' => 'nullable|integer',
        ]);

        $response = $this->api->post("/users/{$id}/roles", $data);

        return response()->json($response->json(), $response->status());
    }

    /**
     * Module 1 (admin): revoke a role from a user — guarded so an admin
     * cannot revoke their own Admin role (would lock themselves out).
     * `roleId` here is the `roles.id` (role type), matching Central-Service's
     * `DELETE /users/{id}/roles/{roleId}` contract (see
     * Internal\UserController::revokeRole), not a per-grant row id.
     */
    public function destroy(Request $request, int $id, int $roleId)
    {
        $user = $request->user();

        if ($id === $user->id) {
            $target = $this->api->get("/users/{$id}")->json();

            $isOwnAdminRole = collect($target['roles'] ?? [])
                ->contains(fn ($role) => (int) $role['id'] === $roleId && $role['role_name'] === 'Admin');

            if ($isOwnAdminRole) {
                return response()->json(['message' => 'You cannot revoke your own Admin role.'], 422);
            }
        }

        $response = $this->api->delete("/users/{$id}/roles/{$roleId}", $request->query());

        return response()->json($response->json(), $response->status());
    }
}
