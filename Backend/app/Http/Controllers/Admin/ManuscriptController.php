<?php

namespace App\Http\Controllers\Admin;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManuscriptController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    /** Admin: return a manuscript to the author for edits (mirrors Editor's "Return to Author"). */
    public function returnToAuthor(Request $request, int $id)
    {
        $user = Auth::guard('remote-sanctum')->user();
        $data = $request->validate(['reason' => 'required|string']);

        $response = $this->api->post("/manuscripts/{$id}/decisions", [
            'editor_id' => $user->id,
            'decision' => 'Return to Edit',
            'decision_letter' => $data['reason'],
        ]);

        return response()->json($response->json(), $response->status());
    }

    public function archive(int $id)
    {
        $response = $this->api->post("/manuscripts/{$id}/archive");

        return response()->json($response->json(), $response->status());
    }

    public function restore(int $id)
    {
        $response = $this->api->post("/manuscripts/{$id}/restore");

        return response()->json($response->json(), $response->status());
    }
}
