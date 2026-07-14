<?php

namespace App\Http\Controllers\Editorial;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use App\Support\Rbac\RoleChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditorAssignmentController extends Controller
{
    public function __construct(
        private readonly ApiClient $api,
        private readonly RoleChecker $roleChecker,
    ) {}

    /**
     * Module 4 function 1: Assign an editor to a manuscript — journal-scoped
     * (the caller must be Editor of *this* manuscript's journal, or Admin).
     */
    public function store(Request $request, int $manuscriptId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'editor_id' => 'required|integer',
            'role' => 'nullable|string',
        ]);

        $manuscript = $this->api->get("/manuscripts/{$manuscriptId}");
        if (! $manuscript->successful()) {
            return response()->json($manuscript->json(), $manuscript->status());
        }
        if (! $user->hasRole('Admin') && ! $this->roleChecker->hasRole($user, 'Editor', $manuscript->json('journal_id'))) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post("/manuscripts/{$manuscriptId}/editor-assignments", $data);

        return response()->json($response->json(), $response->status());
    }
}
