<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditorialDecisionController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function store(Request $request, int $manuscriptId)
    {
        $data = $request->validate([
            'editor_id' => 'required|integer',
            'decision' => 'required|string',
            'decision_letter' => 'nullable|string',
        ]);

        return $this->relay($this->central->post("/manuscripts/{$manuscriptId}/decisions", $data));
    }
}
