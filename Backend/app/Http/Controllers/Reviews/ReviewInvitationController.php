<?php

namespace App\Http\Controllers\Reviews;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use App\Support\Rbac\RoleChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewInvitationController extends Controller
{
    public function __construct(
        private readonly ApiClient $api,
        private readonly RoleChecker $roleChecker,
    ) {}

    /**
     * Reviewer dashboard/history: a Reviewer only ever sees their OWN
     * invitations (reviewer_id is forced to the caller, ignoring any
     * client-supplied value); Editor/Admin may filter by manuscript_id or
     * any reviewer_id (used by ReviewMonitoring).
     */
    public function index(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();
        $query = $request->only(['manuscript_id', 'status', 'per_page']);

        if (! array_intersect(['Editor', 'Admin'], $user->roleNames())) {
            $query['reviewer_id'] = $user->id;
        } elseif ($request->filled('reviewer_id')) {
            $query['reviewer_id'] = $request->query('reviewer_id');
        }

        $response = $this->api->get('/review-invitations', $query);

        return response()->json($response->json(), $response->status());
    }

    public function show(int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();
        $response = $this->api->get("/review-invitations/{$id}");

        if (! $response->successful()) {
            return response()->json($response->json(), $response->status());
        }

        $invitation = $response->json();
        $isOwner = ($invitation['reviewer_id'] ?? null) === $user->id;
        $isEditorish = (bool) array_intersect(['Editor', 'Admin'], $user->roleNames());

        if (! $isOwner && ! $isEditorish) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json($invitation);
    }

    /**
     * Module 3 function 2: Invite reviewer. `role:Editor,Admin` middleware
     * only checks the role name globally; this also confirms the editor is
     * scoped to the manuscript's own journal (Admin bypasses).
     */
    public function store(Request $request, int $manuscriptId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'reviewer_id' => 'required|integer',
            'deadline' => 'required|date',
        ]);

        $manuscript = $this->api->get("/manuscripts/{$manuscriptId}");
        if (! $manuscript->successful()) {
            return response()->json($manuscript->json(), $manuscript->status());
        }
        if (! $user->hasRole('Admin') && ! $this->roleChecker->hasRole($user, 'Editor', $manuscript->json('journal_id'))) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $data['invited_by'] = $user->id;

        $response = $this->api->post("/manuscripts/{$manuscriptId}/review-invitations", $data);

        return response()->json($response->json(), $response->status());
    }

    /** Module 3 function 3: Reviewer accepts/declines (reviewer-only role, see routes/modules/reviews.php). */
    public function respond(Request $request, int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'status' => 'required|string|in:Accepted,Declined',
            'declined_reason' => 'nullable|string',
        ]);

        $invitation = $this->api->get("/review-invitations/{$id}");
        if (! $invitation->successful()) {
            return response()->json($invitation->json(), $invitation->status());
        }
        if ((int) $invitation->json('reviewer_id') !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post("/review-invitations/{$id}/respond", $data);

        return response()->json($response->json(), $response->status());
    }

    /** Module 3 function 4a: Reviewer requests a deadline extension. */
    public function requestExtension(Request $request, int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'requested_deadline' => 'required|date',
            'reason' => 'required|string',
        ]);

        $invitation = $this->api->get("/review-invitations/{$id}");
        if (! $invitation->successful()) {
            return response()->json($invitation->json(), $invitation->status());
        }
        if ((int) $invitation->json('reviewer_id') !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $response = $this->api->post("/review-invitations/{$id}/request-extension", $data);

        return response()->json($response->json(), $response->status());
    }

    /** Module 3 function 4b: Editor approves/rejects the extension request. */
    public function decideExtension(Request $request, int $id)
    {
        $data = $request->validate(['approved' => 'required|boolean']);

        $response = $this->api->post("/review-invitations/{$id}/decide-extension", $data);

        return response()->json($response->json(), $response->status());
    }
}
