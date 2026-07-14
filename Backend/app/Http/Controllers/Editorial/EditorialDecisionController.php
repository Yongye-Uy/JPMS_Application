<?php

namespace App\Http\Controllers\Editorial;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use App\Support\Rbac\RoleChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditorialDecisionController extends Controller
{
    public function __construct(
        private readonly ApiClient $api,
        private readonly RoleChecker $roleChecker,
    ) {}

    /**
     * Module 4 function 2: Make an editorial decision (also covers
     * desk-reject/screening — same endpoint, different `decision` value).
     * Journal-scoped: Editor of *this* manuscript's journal, or Admin.
     */
    public function store(Request $request, int $manuscriptId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'decision' => 'required|string',
            'decision_letter' => 'nullable|string',
        ]);

        $manuscript = $this->api->get("/manuscripts/{$manuscriptId}");
        if (! $manuscript->successful()) {
            return response()->json($manuscript->json(), $manuscript->status());
        }
        if (! $user->hasRole('Admin') && ! $this->roleChecker->hasRole($user, 'Editor', $manuscript->json('journal_id'))) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $data['editor_id'] = $user->id;

        $response = $this->api->post("/manuscripts/{$manuscriptId}/decisions", $data);

        return response()->json($response->json(), $response->status());
    }
}
