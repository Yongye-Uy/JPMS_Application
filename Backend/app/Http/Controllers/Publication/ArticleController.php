<?php

namespace App\Http\Controllers\Publication;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use App\Support\Rbac\RoleChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ApiClient $api,
        private readonly RoleChecker $roleChecker,
    ) {}

    /** Module 5 function 4a: Edit an article's page range — journal-scoped. */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'page_start' => 'nullable|integer',
            'page_end' => 'nullable|integer',
        ]);

        $forbidden = $this->forbidUnlessJournalEditor($id);
        if ($forbidden) {
            return $forbidden;
        }

        $response = $this->api->patch("/articles/{$id}", $data);

        return response()->json($response->json(), $response->status());
    }

    /** Module 5 function 4b: Remove an article from an issue — journal-scoped. */
    public function destroy(int $id)
    {
        $forbidden = $this->forbidUnlessJournalEditor($id);
        if ($forbidden) {
            return $forbidden;
        }

        $response = $this->api->delete("/articles/{$id}");

        return response()->json($response->json(), $response->status());
    }

    private function forbidUnlessJournalEditor(int $articleId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $article = $this->api->get("/articles/{$articleId}");
        if (! $article->successful()) {
            return response()->json($article->json(), $article->status());
        }

        $journalId = $article->json('issue.journal_id');

        if (! $user->hasRole('Admin') && ! $this->roleChecker->hasRole($user, 'Editor', $journalId)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return null;
    }
}
