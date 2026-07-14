<?php

namespace App\Http\Controllers\Reviews;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** Module 3 function 5: Submit review (reviewer-only, and only for the invitation assigned to them). */
    public function store(Request $request, int $invitationId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $invitation = $this->api->get("/review-invitations/{$invitationId}");
        if (! $invitation->successful()) {
            return response()->json($invitation->json(), $invitation->status());
        }
        if ((int) $invitation->json('reviewer_id') !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $request->validate([
            'recommendation' => 'required|string',
            'comments_to_author' => 'required|string',
            'comments_to_editor' => 'nullable|string',
            'scores' => 'required|array',
            'scores.*.criterion' => 'required|string',
            'scores.*.score' => 'required|integer|min:0|max:5',
            'annotated_file' => 'sometimes|file',
        ]);

        $data = $request->except('annotated_file');

        $files = $request->hasFile('annotated_file') ? ['annotated_file' => $request->file('annotated_file')] : [];

        $response = $this->api->postMultipart("/review-invitations/{$invitationId}/reviews", $data, $files);

        return response()->json($response->json(), $response->status());
    }

    /** Module 3 function 6: View a submitted review. */
    public function show(int $id)
    {
        $response = $this->api->get("/reviews/{$id}");

        return response()->json($response->json(), $response->status());
    }

    /** Streams a review's annotated PDF — the reviewer who wrote it, an Editor/Admin, or the manuscript's author/co-authors. */
    public function downloadFile(int $reviewId, int $fileId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $response = $this->api->get("/reviews/{$reviewId}");
        if (! $response->successful()) {
            return response()->json($response->json(), $response->status());
        }
        $review = $response->json();
        $manuscript = $review['invitation']['manuscript'] ?? [];

        $isOwner = (int) ($review['invitation']['reviewer_id'] ?? null) === $user->id;
        $isEditorish = (bool) array_intersect(['Editor', 'Admin'], $user->roleNames());
        $isAuthor = (int) ($manuscript['author_id'] ?? null) === $user->id
            || collect($manuscript['authors'] ?? [])->contains(fn ($a) => (int) $a['user_id'] === $user->id);

        if (! $isOwner && ! $isEditorish && ! $isAuthor) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $download = $this->api->get("/reviews/{$reviewId}/files/{$fileId}/download");

        return response($download->body(), $download->status())
            ->header('Content-Type', $download->header('Content-Type') ?: 'application/pdf');
    }
}
