<?php

namespace App\Http\Controllers\Manuscripts;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManuscriptController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** Module 2 function 1: Search/list submissions — authors see only their own. */
    public function index(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $query = $request->query();

        // NOTE: hasRole('Editor') with no journalId only matches a *global*
        // Editor grant (journal_id === null) — the demo Editor is scoped to
        // journal_id=1, so that check would wrongly fail here. Use
        // roleNames() (role name only, any journal) for "is an Editor at
        // all" checks; use hasRole($role, $journalId) only when a specific
        // journal_id is already known (see the journal-scoped RBAC checks
        // elsewhere in this module).
        $isEditorOrAdmin = array_intersect(['Editor', 'Admin'], $user->roleNames()) !== [];

        if ($request->filled('co_author_id')) {
            // "My co-authored submissions" — always self-scoped, never someone else's.
            $query['co_author_id'] = $user->id;
            unset($query['author_id']);
        } elseif (! $isEditorOrAdmin) {
            $query['author_id'] = $user->id;
        }

        $response = $this->api->get('/manuscripts', $query);

        return response()->json($response->json(), $response->status());
    }

    /** Module 2 function 2: Create submission (draft) — author can only create their own. */
    public function store(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'journal_id' => 'required|integer',
            'manuscript_type' => 'required|string',
            'title' => 'required|string',
            'abstract' => 'nullable|string',
            'keywords' => 'sometimes|array',
        ]);

        $data['author_id'] = $user->id;

        $response = $this->api->post('/manuscripts', $data);

        return response()->json($response->json(), $response->status());
    }

    public function show(int $id)
    {
        $response = $this->api->get("/manuscripts/{$id}");

        return response()->json($response->json(), $response->status());
    }

    /** Module 2 functions 3 + 6: Upload manuscript file / submit revised version. */
    public function storeVersion(Request $request, int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $manuscriptResponse = $this->api->get("/manuscripts/{$id}");
        if (! $manuscriptResponse->successful()) {
            return response()->json($manuscriptResponse->json(), $manuscriptResponse->status());
        }
        $manuscriptData = $manuscriptResponse->json();

        // Enforce that only the primary author or an accepted co-author can upload files.
        $isAuthor = (int) ($manuscriptData['author_id'] ?? null) === $user->id;
        $isCoAuthor = collect($manuscriptData['authors'] ?? [])->contains(fn ($a) => (int) $a['user_id'] === $user->id);

        if (! $isAuthor && ! $isCoAuthor) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // Enforce that files can only be uploaded in Draft or Revision Required status.
        if (! in_array($manuscriptData['status'] ?? null, ['Draft', 'Revision Required'], true)) {
            return response()->json(['message' => 'Files can only be uploaded while the manuscript is in Draft or Revision Required status.'], 422);
        }

        $request->validate([
            'response_note' => 'nullable|string',
            'main_file' => 'required|file|mimes:pdf',
            'supplementary_files' => 'sometimes|array',
        ]);

        $files = ['main_file' => $request->file('main_file')];
        if ($request->hasFile('supplementary_files')) {
            $files['supplementary_files'] = $request->file('supplementary_files');
        }

        $data = $request->only(['response_note']);
        $data['uploaded_by'] = $user->id;

        $response = $this->api->postMultipart("/manuscripts/{$id}/versions", $data, $files);

        return response()->json($response->json(), $response->status());
    }

    /** Streams a manuscript file's bytes to anyone with a legitimate reason to see it. */
    public function downloadFile(int $id, int $fileId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $manuscript = $this->api->get("/manuscripts/{$id}");
        if (! $manuscript->successful()) {
            return response()->json($manuscript->json(), $manuscript->status());
        }
        $data = $manuscript->json();

        $file = collect($data['versions'] ?? [])
            ->flatMap(fn ($v) => $v['files'] ?? [])
            ->firstWhere('id', $fileId);

        if (! $file) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if (! $this->canAccessManuscriptFiles($data, $user)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $download = $this->api->get("/manuscripts/{$id}/files/{$fileId}/download");

        return response($download->body(), $download->status())
            ->header('Content-Type', $download->header('Content-Type') ?: 'application/pdf');
    }

    /** Broad read access: primary author, any accepted co-author, any Editor/Admin, an assigned editor, a non-declined reviewer. */
    private function canAccessManuscriptFiles(array $manuscript, $user): bool
    {
        // Any Editor or Admin role holder may read manuscript files — matches the
        // same pattern used in ReviewController::downloadFile().
        if (array_intersect(['Editor', 'Admin'], $user->roleNames()) !== []) {
            return true;
        }
        if ((int) ($manuscript['author_id'] ?? null) === $user->id) {
            return true;
        }
        if (collect($manuscript['authors'] ?? [])->contains(fn ($a) => (int) $a['user_id'] === $user->id)) {
            return true;
        }
        if (collect($manuscript['editor_assignments'] ?? [])->contains(fn ($e) => (int) $e['editor_id'] === $user->id)) {
            return true;
        }
        if (collect($manuscript['review_invitations'] ?? [])->contains(
            fn ($r) => (int) $r['reviewer_id'] === $user->id && $r['status'] !== 'Declined'
        )) {
            return true;
        }

        return false;
    }

    /** Module 2 function 8: Set which uploaded version is the official one — main author only. */
    public function setMainVersion(int $id, int $versionId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $manuscript = $this->api->get("/manuscripts/{$id}");
        if (! $manuscript->successful()) {
            return response()->json($manuscript->json(), $manuscript->status());
        }
        if ((int) $manuscript->json('author_id') !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post("/manuscripts/{$id}/versions/{$versionId}/set-main");

        return response()->json($response->json(), $response->status());
    }

    /** Module 2 function 4: Invite co-author. */
    public function inviteCoAuthor(Request $request, int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'invited_author_id' => 'required|integer',
        ]);

        if ($data['invited_author_id'] === $user->id) {
            return response()->json(['message' => 'You cannot invite yourself as a co-author.'], 422);
        }

        $data['inviting_author_id'] = $user->id;

        $response = $this->api->post("/manuscripts/{$id}/co-authors/invite", $data);

        return response()->json($response->json(), $response->status());
    }

    /** Module 2 function 5: Submit for review — main author only. */
    public function submit(int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $manuscript = $this->api->get("/manuscripts/{$id}");
        if (! $manuscript->successful()) {
            return response()->json($manuscript->json(), $manuscript->status());
        }
        if ((int) $manuscript->json('author_id') !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post("/manuscripts/{$id}/submit");

        return response()->json($response->json(), $response->status());
    }

    /** Module 2 function 7: Withdraw — main author only. */
    public function withdraw(Request $request, int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate(['reason' => 'required|string']);

        $manuscript = $this->api->get("/manuscripts/{$id}");
        if (! $manuscript->successful()) {
            return response()->json($manuscript->json(), $manuscript->status());
        }
        if ((int) $manuscript->json('author_id') !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post("/manuscripts/{$id}/withdraw", $data);

        return response()->json($response->json(), $response->status());
    }

    /** Reopens a Withdrawn manuscript back to Draft — main author only. */
    public function resubmit(int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $manuscript = $this->api->get("/manuscripts/{$id}");
        if (! $manuscript->successful()) {
            return response()->json($manuscript->json(), $manuscript->status());
        }
        if ((int) $manuscript->json('author_id') !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post("/manuscripts/{$id}/resubmit");

        return response()->json($response->json(), $response->status());
    }

    /** Author deletes a manuscript that never left Draft — main author only. */
    public function destroy(int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $manuscript = $this->api->get("/manuscripts/{$id}");
        if (! $manuscript->successful()) {
            return response()->json($manuscript->json(), $manuscript->status());
        }
        if ((int) $manuscript->json('author_id') !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->delete("/manuscripts/{$id}");

        return response()->json($response->json(), $response->status());
    }
}
