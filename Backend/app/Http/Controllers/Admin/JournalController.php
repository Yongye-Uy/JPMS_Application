<?php

namespace App\Http\Controllers\Admin;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    public function index(Request $request)
    {
        $response = $this->api->get('/journals', $request->query());

        return response()->json($response->json(), $response->status());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'issn' => 'nullable|string',
            'scope_description' => 'nullable|string',
            'editor_in_chief_id' => 'nullable|integer',
        ]);

        $response = $this->api->post('/journals', $data);

        return response()->json($response->json(), $response->status());
    }

    public function show(int $id)
    {
        $response = $this->api->get("/journals/{$id}");

        return response()->json($response->json(), $response->status());
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'issn' => 'sometimes|nullable|string',
            'scope_description' => 'nullable|string',
            'editor_in_chief_id' => 'nullable|integer',
        ]);

        $response = $this->api->patch("/journals/{$id}", $data);

        return response()->json($response->json(), $response->status());
    }

    public function archive(int $id)
    {
        $response = $this->api->post("/journals/{$id}/archive");

        return response()->json($response->json(), $response->status());
    }

    public function restore(int $id)
    {
        $response = $this->api->post("/journals/{$id}/restore");

        return response()->json($response->json(), $response->status());
    }
}
