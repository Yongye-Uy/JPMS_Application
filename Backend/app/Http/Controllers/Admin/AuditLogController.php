<?php

namespace App\Http\Controllers\Admin;

use App\Clients\ApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct(private readonly ApiClient $api) {}

    public function index(Request $request)
    {
        $response = $this->api->get('/audit-log', $request->query());

        return response()->json($response->json(), $response->status());
    }
}
