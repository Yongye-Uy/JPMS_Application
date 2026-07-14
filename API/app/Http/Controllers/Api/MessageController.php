<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function index(int $manuscriptId)
    {
        return $this->relay($this->central->get("/manuscripts/{$manuscriptId}/messages"));
    }

    public function store(Request $request, int $manuscriptId)
    {
        $data = $request->validate([
            'sender_id' => 'required|integer',
            'recipient_id' => 'required|integer',
            'subject' => 'nullable|string',
            'body' => 'required|string',
        ]);

        return $this->relay($this->central->post("/manuscripts/{$manuscriptId}/messages", $data));
    }

    public function markRead(int $id)
    {
        return $this->relay($this->central->post("/messages/{$id}/read"));
    }
}
