<?php

namespace App\Http\Controllers\Publication;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use App\Support\Rbac\RoleChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssueController extends Controller
{
    public function __construct(
        private readonly ApiClient $api,
        private readonly RoleChecker $roleChecker,
    ) {}

    /**
     * Module 5 function 1: Create an issue — journal-scoped (Editor of
     * *this* journal, or Admin).
     *
     * GET issues / GET issues/{id} are intentionally not duplicated here —
     * see App\Http\Controllers\Reader\IssueController and the note in
     * routes/modules/publication.php.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'journal_id' => 'required|integer',
            'volume' => 'required|integer',
            'number' => 'required|integer',
            'year' => 'required|integer',
            'publication_date' => 'nullable|date',
        ]);

        $isEditorOrAdmin = $user->hasRole('Admin')
            || $this->roleChecker->hasRole($user, 'Editor', $data['journal_id'])
            || in_array('Editor', $user->roleNames(), true);
        if (! $isEditorOrAdmin) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post('/issues', $data);

        return response()->json($response->json(), $response->status());
    }

    /** Module 5 function 2: Add an accepted article to an issue (DOI auto-assigned by Central-Service). */
    public function addArticle(Request $request, int $issueId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'manuscript_id' => 'required|integer',
            'page_start' => 'nullable|integer',
            'page_end' => 'nullable|integer',
        ]);

        $issue = $this->api->get("/issues/{$issueId}");
        if (! $issue->successful()) {
            return response()->json($issue->json(), $issue->status());
        }
        $isEditorOrAdmin = $user->hasRole('Admin')
            || $this->roleChecker->hasRole($user, 'Editor', $issue->json('journal_id'))
            || in_array('Editor', $user->roleNames(), true);
        if (! $isEditorOrAdmin) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post("/issues/{$issueId}/articles", $data);

        return response()->json($response->json(), $response->status());
    }

    /** Module 5 function 3: Publish an issue — journal-scoped. */
    public function publish(int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $issue = $this->api->get("/issues/{$id}");
        if (! $issue->successful()) {
            return response()->json($issue->json(), $issue->status());
        }
        $isEditorOrAdmin = $user->hasRole('Admin')
            || $this->roleChecker->hasRole($user, 'Editor', $issue->json('journal_id'))
            || in_array('Editor', $user->roleNames(), true);
        if (! $isEditorOrAdmin) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post("/issues/{$id}/publish");

        return response()->json($response->json(), $response->status());
    }
}
