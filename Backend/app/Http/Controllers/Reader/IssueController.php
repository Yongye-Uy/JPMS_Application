<?php

namespace App\Http\Controllers\Reader;

use App\Auth\RemoteUser;
use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssueController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /**
     * Public: browse issues (e.g. table of contents by journal).
     *
     * This is also the endpoint Editor/Admin callers use to manage issues
     * (routes/modules/publication.php only defines the write actions, not
     * GET, to avoid a duplicate route registration — see the note there).
     * Anonymous/Reader/Author callers only ever see Published issues;
     * logged-in Editor/Admin callers see everything, including Drafts,
     * since Central-Service's own `status` filter is opt-in, not enforced
     * server-side (see Central-Service Internal\IssueController::index).
     */
    public function index(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();
        $query = $request->query();

        if (! $this->isEditorOrAdmin($user)) {
            $query['status'] = 'Published';
        }

        $response = $this->api->get('/issues', $query);

        return response()->json($response->json(), $response->status());
    }

    public function show(int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $response = $this->api->get("/issues/{$id}");

        if ($response->successful() && ! $this->isEditorOrAdmin($user)) {
            if (($response->json('status') ?? null) !== 'Published') {
                return response()->json(['message' => 'Not found.'], 404);
            }
        }

        return response()->json($response->json(), $response->status());
    }

    /**
     * True if the (possibly null) caller holds Editor or Admin for ANY
     * journal. Deliberately uses roleNames() rather than
     * RemoteUser::hasRole('Editor') with no journalId — the latter only
     * matches a *global* Editor grant, but Editor grants in this system are
     * always journal-scoped, so a bare hasRole('Editor') would incorrectly
     * return false for every real Editor.
     */
    private function isEditorOrAdmin(?RemoteUser $user): bool
    {
        return $user !== null && array_intersect(['Editor', 'Admin'], $user->roleNames()) !== [];
    }
}
