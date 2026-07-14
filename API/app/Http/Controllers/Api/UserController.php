<?php

namespace App\Http\Controllers\Api;

use App\Clients\CentralServiceClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly CentralServiceClient $central) {}

    public function index(Request $request)
    {
        return $this->relay($this->central->get('/users', $request->query()));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string',
            'affiliation' => 'nullable|string',
            'country' => 'nullable|string',
            'contact_info' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'roles' => 'sometimes|array',
        ]);

        return $this->relay($this->central->post('/users', $data));
    }

    public function show(int $id)
    {
        return $this->relay($this->central->get("/users/{$id}"));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'full_name' => 'sometimes|string',
            'affiliation' => 'nullable|string',
            'country' => 'nullable|string',
            'contact_info' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'password' => 'sometimes|string|min:6',
        ]);

        return $this->relay($this->central->patch("/users/{$id}", $data));
    }

    public function assignRole(Request $request, int $id)
    {
        $data = $request->validate([
            'role_name' => 'required|string',
            'journal_id' => 'nullable|integer',
        ]);

        return $this->relay($this->central->post("/users/{$id}/roles", $data));
    }

    public function revokeRole(Request $request, int $id, int $roleId)
    {
        return $this->relay($this->central->delete("/users/{$id}/roles/{$roleId}", $request->query()));
    }
}
