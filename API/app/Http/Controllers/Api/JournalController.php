<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function index(Request $request)
    {
        return $this->relay($this->central->get('/journals', $request->query()));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'issn' => 'nullable|string',
            'scope_description' => 'nullable|string',
            'editor_in_chief_id' => 'nullable|integer',
        ]);

        return $this->relay($this->central->post('/journals', $data));
    }

    public function show(int $id)
    {
        return $this->relay($this->central->get("/journals/{$id}"));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'issn' => 'sometimes|nullable|string',
            'scope_description' => 'nullable|string',
            'editor_in_chief_id' => 'nullable|integer',
        ]);

        return $this->relay($this->central->patch("/journals/{$id}", $data));
    }

    public function archive(int $id)
    {
        return $this->relay($this->central->post("/journals/{$id}/archive"));
    }

    public function restore(int $id)
    {
        return $this->relay($this->central->post("/journals/{$id}/restore"));
    }
}
