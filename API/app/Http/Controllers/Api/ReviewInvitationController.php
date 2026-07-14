<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewInvitationController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function index(Request $request)
    {
        return $this->relay($this->central->get('/review-invitations', $request->query()));
    }

    public function show(int $id)
    {
        return $this->relay($this->central->get("/review-invitations/{$id}"));
    }

    public function store(Request $request, int $manuscriptId)
    {
        $data = $request->validate([
            'reviewer_id' => 'required|integer',
            'invited_by' => 'required|integer',
            'deadline' => 'required|date',
        ]);

        return $this->relay($this->central->post("/manuscripts/{$manuscriptId}/review-invitations", $data));
    }

    public function respond(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:Accepted,Declined',
            'declined_reason' => 'nullable|string',
        ]);

        return $this->relay($this->central->post("/review-invitations/{$id}/respond", $data));
    }

    public function requestExtension(Request $request, int $id)
    {
        $data = $request->validate([
            'requested_deadline' => 'required|date',
            'reason' => 'required|string',
        ]);

        return $this->relay($this->central->post("/review-invitations/{$id}/request-extension", $data));
    }

    public function decideExtension(Request $request, int $id)
    {
        $data = $request->validate(['approved' => 'required|boolean']);

        return $this->relay($this->central->post("/review-invitations/{$id}/decide-extension", $data));
    }
}
