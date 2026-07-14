<?php

namespace App\Http\Controllers\Manuscripts;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoAuthorInvitationController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** A user only ever sees their OWN co-author invitations (invited_author_id forced to the caller). */
    public function index(Request $request)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $response = $this->api->get('/co-author-invitations', [
            'invited_author_id' => $user->id,
            'status' => $request->query('status'),
        ]);

        return response()->json($response->json(), $response->status());
    }

    /** Module 2 function 4 (part 2): Respond to a co-author invitation. */
    public function respond(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:Accepted,Declined',
            'author_order' => 'nullable|integer',
            'is_corresponding' => 'sometimes|boolean',
        ]);

        $response = $this->api->post("/co-author-invitations/{$id}/respond", $data);

        return response()->json($response->json(), $response->status());
    }
}
