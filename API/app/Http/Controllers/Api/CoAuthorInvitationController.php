<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoAuthorInvitationController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function index(Request $request)
    {
        return $this->relay($this->central->get('/co-author-invitations', $request->query()));
    }

    public function respond(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:Accepted,Declined',
            'author_order' => 'nullable|integer',
            'is_corresponding' => 'sometimes|boolean',
        ]);

        return $this->relay($this->central->post("/co-author-invitations/{$id}/respond", $data));
    }
}
