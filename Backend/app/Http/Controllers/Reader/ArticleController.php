<?php

namespace App\Http\Controllers\Reader;

use App\Auth\RemoteUser;
use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /**
     * Module 6 function 1: Public search/browse published articles.
     * `include_unpublished` is only honoured for Editor/Admin callers —
     * otherwise a client could pass it to see in-review manuscripts.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $query = $request->only(['q', 'journal_id', 'issue_id', 'include_unpublished', 'year', 'page', 'per_page']);

        if (! $this->isEditorOrAdmin($user)) {
            unset($query['include_unpublished']);
        }

        $response = $this->api->get('/articles', $query);

        return response()->json($response->json(), $response->status());
    }

    /**
     * Module 6 function 2: Public article detail — Central-Service's `show`
     * doesn't filter by published_at (unlike `index`), so this enforces it
     * here for non-Editor/Admin callers to avoid leaking unpublished articles.
     */
    public function show(int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $response = $this->api->get("/articles/{$id}");

        if ($response->successful() && ! $this->isEditorOrAdmin($user) && ! $response->json('published_at')) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * Module 6: Public inline PDF viewer for the `<embed>` on the article
     * page — no login required, unlike download(). Reachable directly by
     * URL, so it re-checks published_at itself rather than trusting that the
     * article page already gated access.
     */
    public function view(int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $article = $this->api->get("/articles/{$id}");
        if ($article->successful() && ! $this->isEditorOrAdmin($user) && ! $article->json('published_at')) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $response = $this->api->get("/articles/{$id}/download");

        if (! $response->successful()) {
            return response()->json($response->json(), $response->status());
        }

        return response($response->body(), 200)
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/pdf');
    }

    /**
     * Module 6 function 4: Download the article PDF — requires login. This
     * route lives outside the `auth:remote-sanctum` group (the whole
     * reader.php file is public), so the auth check happens manually here.
     */
    public function download(Request $request, int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $response = $this->api->get("/articles/{$id}/download", ['disposition' => 'attachment']);

        if (! $response->successful()) {
            return response()->json($response->json(), $response->status());
        }

        $this->api->post("/articles/{$id}/metrics/track", ['event' => 'download']);

        return response($response->body(), 200)
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/pdf')
            ->header('Content-Disposition', $response->header('Content-Disposition') ?: "attachment; filename=\"article-{$id}.pdf\"");
    }

    /** Module 6/7: Track a view/download/citation event — public, no auth required. */
    public function trackView(Request $request, int $id)
    {
        $data = $request->validate(['event' => 'sometimes|string|in:view,download,citation']);

        $response = $this->api->post("/articles/{$id}/metrics/track", ['event' => $data['event'] ?? 'view']);

        return response()->json($response->json(), $response->status());
    }

    /** Read-only refresh of today's counts, e.g. after a download opened in a separate tab. */
    public function todayMetrics(int $id)
    {
        $response = $this->api->get("/articles/{$id}/metrics/today");

        return response()->json($response->json(), $response->status());
    }

    /**
     * True if the (possibly null) caller holds Editor or Admin for ANY
     * journal. Uses roleNames() rather than RemoteUser::hasRole('Editor')
     * with no journalId — the latter only matches a *global* Editor grant,
     * but Editor grants in this system are always journal-scoped, so a bare
     * hasRole('Editor') would incorrectly return false for every real Editor.
     */
    private function isEditorOrAdmin(?RemoteUser $user): bool
    {
        return $user !== null && array_intersect(['Editor', 'Admin'], $user->roleNames()) !== [];
    }
}
