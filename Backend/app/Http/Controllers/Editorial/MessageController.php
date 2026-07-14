<?php

namespace App\Http\Controllers\Editorial;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** Module 4 function 3a: List messages for a manuscript. */
    public function index(int $manuscriptId)
    {
        $response = $this->api->get("/manuscripts/{$manuscriptId}/messages");

        return response()->json($response->json(), $response->status());
    }

    /** Module 4 function 3b: Send a message. */
    public function store(Request $request, int $manuscriptId)
    {
        $user = Auth::guard('remote-sanctum')->user();

        $data = $request->validate([
            'recipient_id' => 'required|integer',
            'subject' => 'nullable|string',
            'body' => 'required|string',
        ]);

        $data['sender_id'] = $user->id;

        $response = $this->api->post("/manuscripts/{$manuscriptId}/messages", $data);

        return response()->json($response->json(), $response->status());
    }

    /** Module 4 function 3c: Mark a message read. */
    public function markRead(int $id)
    {
        $response = $this->api->post("/messages/{$id}/read");

        return response()->json($response->json(), $response->status());
    }
}
