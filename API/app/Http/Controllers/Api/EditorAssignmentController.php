<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditorAssignmentController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function store(Request $request, int $manuscriptId)
    {
        $data = $request->validate([
            'editor_id' => 'required|integer',
            'role' => 'nullable|string',
        ]);

        return $this->relay($this->central->post("/manuscripts/{$manuscriptId}/editor-assignments", $data));
    }
}
